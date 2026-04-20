<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Presentation;
use App\Models\PresentationScore;
use App\Services\PontuacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function show(Contest $contest)
    {
        if ($contest->status === 'FINALIZADO') {
            return response()->json(['message' => 'O concurso já foi finalizado. Avaliação encerrada.'], 403);
        }

        if (!Auth::user()->hasRole('admin') && !$contest->jurors()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Você não está vinculado como jurado deste concurso.');
        }

        $currentPresentation = Presentation::with(['competitor', 'contest.evaluationCriteria'])
            ->find($contest->current_presentation_id);

        $hasVoted = false;
        if ($currentPresentation) {
            $hasVoted = PresentationScore::where('presentation_id', $currentPresentation->id)
                ->where('juror_id', Auth::id())
                ->exists();
        }

        return response()->json([
            'contest' => $contest->load('evaluationCriteria'),
            'currentPresentation' => $currentPresentation,
            'hasVoted' => $hasVoted
        ]);
    }

    public function submit(Request $request, Contest $contest)
    {
        $currentPresentation = Presentation::find($contest->current_presentation_id);
        
        if (!$currentPresentation) {
            return response()->json(['message' => 'Nenhuma apresentação no palco.'], 422);
        }

        $hasVoted = PresentationScore::where('presentation_id', $currentPresentation->id)
            ->where('juror_id', Auth::id())
            ->exists();

        if ($hasVoted) {
            return response()->json(['message' => 'Você já votou nesta apresentação.'], 422);
        }

        // Validação Dinâmica
        $rules = [];
        $messages = [];
        foreach ($contest->evaluationCriteria as $criterion) {
            $rules["scores.{$criterion->id}"] = "required|numeric|min:0|max:{$criterion->max_score}";
        }

        $request->validate($rules);

        try {
            PontuacaoService::run(
                $currentPresentation,
                Auth::user(),
                $request->scores
            );

            return response()->json(['message' => 'Notas enviadas com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
