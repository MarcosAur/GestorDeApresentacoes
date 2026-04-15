<?php

namespace App\Services;

use App\Models\Presentation;
use Illuminate\Support\Facades\Auth;

class PresentationService
{
    /**
     * Create a new presentation.
     *
     * @param array $validatedData
     * @return Presentation
     */
    public static function run(array $validatedData): Presentation
    {
        return Presentation::create([
            'contest_id' => $validatedData['contest_id'],
            'competitor_id' => Auth::id(),
            'work_title' => $validatedData['work_title'],
            'status' => 'EM_ANALISE',
        ]);
    }

    /**
     * Evaluate a presentation.
     *
     * @param Presentation $presentation
     * @param array $validatedData
     * @return bool
     */
    public static function evaluate(Presentation $presentation, array $validatedData): bool
    {
        return $presentation->update([
            'status' => $validatedData['status'],
            'justification_inapto' => $validatedData['status'] === 'INAPTO' 
                ? $validatedData['justification_inapto'] 
                : null,
        ]);
    }
}
