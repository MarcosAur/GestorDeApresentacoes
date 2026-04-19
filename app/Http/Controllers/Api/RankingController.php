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
     * Retorna o ranking público apenas se o concurso estiver FINALIZADO.
     */
    public function public(Contest $contest)
    {
        if ($contest->status !== 'FINALIZADO') {
            return response()->json([
                'message' => 'O ranking estará disponível após a finalização do concurso.',
                'status' => $contest->status
            ], 403);
        }

        $ranking = PontuacaoService::getRanking($contest);

        return response()->json([
            'contest' => $contest,
            'ranking' => $ranking
        ]);
    }
}
