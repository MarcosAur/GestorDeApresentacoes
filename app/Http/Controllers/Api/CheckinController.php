<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function process(Request $request)
    {
        $request->validate(['hash' => 'required|string']);
        $hash = $request->hash;

        $presentation = Presentation::where('qr_code_hash', $hash)->with('competitor')->first();

        if (!$presentation) {
            return response()->json(['message' => 'QR Code inválido ou não encontrado.'], 404);
        }

        if ($presentation->checkin_realizado) {
            return response()->json(['message' => "Check-in já realizado para: {$presentation->competitor->name}"], 422);
        }

        $presentation->update(['checkin_realizado' => true]);

        return response()->json([
            'message' => "Check-in confirmado: {$presentation->competitor->name} ({$presentation->work_title})",
            'presentation' => $presentation
        ]);
    }
}
