<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Services\PontuacaoService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * Retorna o ranking em tempo real para o Admin.
     */
    public function admin(Contest $contest)
    {
        $ranking = PontuacaoService::getRanking($contest);
        
        return response()->json([
            'contest' => $contest,
            'ranking' => $ranking
        ]);
    }

    /**
     * Retorna a lista de rankings liberados para o público.
     */
    public function indexPublic()
    {
        $contests = Contest::where('status', 'FINALIZADO')
            ->where('ranking_released', true)
            ->with('event')
            ->latest()
            ->get();

        return response()->json($contests);
    }

    /**
     * Alterna o status de liberação do ranking (apenas Admin).
     */
    public function toggleRelease(Contest $contest)
    {
        $contest->update([
            'ranking_released' => !$contest->ranking_released
        ]);

        return response()->json([
            'message' => $contest->ranking_released ? 'Ranking liberado!' : 'Ranking ocultado.',
            'ranking_released' => $contest->ranking_released
        ]);
    }

    /**
     * Retorna o ranking público apenas se o concurso estiver FINALIZADO e LIBERADO.
     */
    public function public(Contest $contest)
    {
        if ($contest->status !== 'FINALIZADO' || !$contest->ranking_released) {
            return response()->json([
                'message' => 'O ranking não está disponível para visualização pública.',
                'status' => $contest->status,
                'released' => $contest->ranking_released
            ], 403);
        }

        $ranking = PontuacaoService::getRanking($contest);

        return response()->json([
            'contest' => $contest,
            'ranking' => $ranking
        ]);
    }
}
