<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Presentation;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function stage(Contest $contest)
    {
        $currentPresentation = Presentation::with(['competitor'])
            ->find($contest->current_presentation_id);

        return response()->json([
            'contest' => $contest,
            'currentPresentation' => $currentPresentation
        ]);
    }
}
