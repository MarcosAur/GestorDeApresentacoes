<?php

namespace App\Livewire\Admin;

use App\Models\Presentation;
use App\Services\PresentationService;
use Livewire\Component;

class PresentationAnalyzer extends Component
{
    public $selectedPresentation;
    public $status;
    public $justification_inapto;

    public function selectPresentation(Presentation $presentation)
    {
        $this->selectedPresentation = $presentation;
        $this->status = $presentation->status;
        $this->justification_inapto = $presentation->justification_inapto;
    }

    public function evaluate()
    {
        $this->validate([
            'status' => 'required|in:APTO,INAPTO',
            'justification_inapto' => 'required_if:status,INAPTO|string|nullable',
        ]);

        PresentationService::evaluate($this->selectedPresentation, [
            'status' => $this->status,
            'justification_inapto' => $this->justification_inapto,
        ]);

        $this->selectedPresentation = null;
        session()->flash('message', 'Apresentação avaliada com sucesso!');
    }

    public function render()
    {
        return view('livewire.admin.presentation-analyzer', [
            'presentations' => Presentation::where('status', 'EM_ANALISE')->with(['competitor', 'contest'])->get(),
        ]);
    }
}
