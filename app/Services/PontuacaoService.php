<?php

namespace App\Services;

use App\Models\Presentation;
use App\Models\PresentationScore;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Support\Facades\DB;

class PontuacaoService
{
    /**
     * Registra as notas de um jurado para uma apresentação.
     */
    public static function run(Presentation $presentation, User $juror, array $scores): void
    {
        DB::transaction(function () use ($presentation, $juror, $scores) {
            // Validar se a apresentação está no palco
            $contest = $presentation->contest;
            if ($contest->current_presentation_id !== $presentation->id || $contest->status !== 'EM_ANDAMENTO') {
                throw new \Exception('Esta apresentação não está ativa no palco ou o concurso não está em andamento.');
            }

            // Validar se o jurado já votou
            $alreadyVoted = PresentationScore::where('presentation_id', $presentation->id)
                ->where('juror_id', $juror->id)
                ->exists();

            if ($alreadyVoted) {
                throw new \Exception('Você já enviou suas notas para esta apresentação.');
            }

            // Registrar cada nota
            foreach ($scores as $criterionId => $score) {
                PresentationScore::create([
                    'presentation_id' => $presentation->id,
                    'juror_id' => $juror->id,
                    'criterion_id' => $criterionId,
                    'assigned_score' => $score,
                ]);
            }

            broadcast(new \App\Events\NotaAtribuida($contest->id))->toOthers();
        });
    }

    /**
     * Verifica se todos os jurados vinculados ao concurso já votaram na apresentação atual.
     */
    public static function checkAllJurorsVoted(Contest $contest): bool
    {
        if (!$contest->current_presentation_id) {
            return false;
        }

        $linkedJurorsCount = $contest->jurors()->count();
        
        $jurorsWhoVotedCount = PresentationScore::where('presentation_id', $contest->current_presentation_id)
            ->distinct('juror_id')
            ->count('juror_id');

        return $jurorsWhoVotedCount >= $linkedJurorsCount;
    }

    /**
     * Calcula o ranking das apresentações de um concurso.
     */
    public static function getRanking(Contest $contest)
    {
        $presentations = Presentation::where('contest_id', $contest->id)
            ->where('status', 'APTO')
            ->where('checkin_realizado', true)
            ->with(['scores.criterion', 'competitor'])
            ->get();

        $ranked = $presentations->map(function ($presentation) {
            $totalPonderado = 0;
            $votosPorCriterio = [];

            foreach ($presentation->scores as $score) {
                $valorPonderado = $score->assigned_score * $score->criterion->weight;
                $totalPonderado += $valorPonderado;
                
                // Agrupar para desempate (média das notas dos jurados para aquele critério)
                if (!isset($votosPorCriterio[$score->criterion_id])) {
                    $votosPorCriterio[$score->criterion_id] = [];
                }
                $votosPorCriterio[$score->criterion_id][] = $score->assigned_score;
            }

            $criteriosDesempate = [];
            foreach ($votosPorCriterio as $cid => $votos) {
                $criteriosDesempate[$cid] = array_sum($votos) / count($votos);
            }

            return [
                'presentation' => $presentation,
                'total_score' => $totalPonderado,
                'tiebreakers' => $criteriosDesempate
            ];
        });

        // Ordenação
        return $ranked->sort(function ($a, $b) use ($contest) {
            // 1. Nota Total
            if ($b['total_score'] != $a['total_score']) {
                return $b['total_score'] <=> $a['total_score'];
            }

            // 2. Desempate por Prioridade de Critério
            $criteriaOrdered = $contest->evaluationCriteria()->orderBy('tiebreak_priority')->get();
            
            foreach ($criteriaOrdered as $criterion) {
                $scoreA = $a['tiebreakers'][$criterion->id] ?? 0;
                $scoreB = $b['tiebreakers'][$criterion->id] ?? 0;
                
                if ($scoreB != $scoreA) {
                    return $scoreB <=> $scoreA;
                }
            }

            return 0;
        })->values();
    }
}
