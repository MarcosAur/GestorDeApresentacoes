<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Presentation;
use App\Models\PresentationScore;
use App\Events\ApresentacaoAlterada;
use App\Services\PontuacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StageController extends Controller
{
    public function show(Contest $contest)
    {
        $contest->load(['jurors', 'evaluationCriteria', 'currentPresentation.competitor']);
        
        $presentations = Presentation::where('contest_id', $contest->id)
            ->where('status', 'APTO')
            ->with('competitor')
            ->get();

        $votedJurorIds = [];
        if ($contest->current_presentation_id) {
            $votedJurorIds = PresentationScore::where('presentation_id', $contest->current_presentation_id)
                ->distinct('juror_id')
                ->pluck('juror_id')
                ->toArray();
        }

        return response()->json([
            'contest' => $contest,
            'presentations' => $presentations,
            'votedJurorIds' => $votedJurorIds
        ]);
    }

    public function setOnStage(Request $request, Contest $contest)
    {
        $request->validate(['presentation_id' => 'required|exists:presentations,id']);
        $presentationId = $request->presentation_id;

        $presentation = Presentation::findOrFail($presentationId);
        if (!$presentation->checkin_realizado) {
            return response()->json(['message' => 'O competidor deve realizar o check-in antes de ir para o palco.'], 422);
        }

        // Validar se todos votaram antes de trocar (se houver uma apresentação atual)
        if ($contest->current_presentation_id && !PontuacaoService::checkAllJurorsVoted($contest)) {
            return response()->json(['message' => 'Aguardando votos de todos os jurados para trocar.'], 422);
        }

        // Finalizar a apresentação anterior
        if ($contest->current_presentation_id) {
            Presentation::where('id', $contest->current_presentation_id)
                ->update(['status' => 'FINALIZADA']);
        }

        $contest->current_presentation_id = $presentationId;
        
        if ($contest->status === 'AGENDADO') {
            $contest->status = 'EM_ANDAMENTO';
        }

        $contest->save();

        broadcast(new ApresentacaoAlterada($contest->id, $presentationId));
        
        return $this->show($contest);
    }

    public function finishContest(Contest $contest)
    {
        if ($contest->current_presentation_id && !PontuacaoService::checkAllJurorsVoted($contest)) {
            return response()->json(['message' => 'Aguardando votos finais dos jurados.'], 422);
        }

        // Finalizar a última apresentação
        if ($contest->current_presentation_id) {
            Presentation::where('id', $contest->current_presentation_id)
                ->update(['status' => 'FINALIZADA']);
        }

        $contest->status = 'FINALIZADO';
        $contest->current_presentation_id = null;
        $contest->save();

        broadcast(new ApresentacaoAlterada($contest->id, null));

        return response()->json(['message' => 'Concurso Finalizado!']);
    }
}
