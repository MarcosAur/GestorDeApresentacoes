<?php

namespace App\Livewire\Admin;

use App\Livewire\Forms\EvaluationForm;
use App\Models\Presentation;
use App\Services\PresentationService;
use Livewire\Component;

class PresentationAnalyzer extends Component
{
    public EvaluationForm $form;
    public $selectedPresentation;

    public function selectPresentation(Presentation $presentation)
    {
        $this->selectedPresentation = $presentation;
        $this->form->setPresentation($presentation);
    }

    public function evaluate()
    {
        $this->form->evaluate($this->selectedPresentation);

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
