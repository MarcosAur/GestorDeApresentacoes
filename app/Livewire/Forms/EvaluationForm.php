<?php

namespace App\Livewire\Forms;

use App\Models\Presentation;
use App\Services\PresentationService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EvaluationForm extends Form
{
    #[Validate('required|in:APTO,INAPTO')]
    public $status = 'EM_ANALISE';

    #[Validate('required_if:status,INAPTO|string|nullable')]
    public $justification_inapto = '';

    public function setPresentation(Presentation $presentation)
    {
        $this->status = $presentation->status;
        $this->justification_inapto = $presentation->justification_inapto;
    }

    public function evaluate(Presentation $presentation)
    {
        $this->validate();

        PresentationService::evaluate($presentation, [
            'status' => $this->status,
            'justification_inapto' => $this->justification_inapto,
        ]);

        $this->reset();
    }
}
