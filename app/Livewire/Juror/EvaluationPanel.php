<?php

namespace App\Livewire\Juror;

use App\Models\Contest;
use App\Models\Presentation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class EvaluationPanel extends Component
{
    public Contest $contest;
    public ?Presentation $currentPresentation = null;
    
    // Placeholder for evaluation scores (Task 5)
    public array $scores = [];
    public bool $hasVoted = false;

    public function mount(Contest $contest)
    {
        // Validar se o jurado está vinculado ao concurso
        if (!auth()->user()->hasRole('admin') && !$contest->jurors()->where('user_id', auth()->id())->exists()) {
            abort(403, 'Você não está vinculado como jurado deste concurso.');
        }

        $this->contest = $contest;
        $this->loadCurrentPresentation();
    }

    #[On('echo:concurso.{contest.id},ApresentacaoAlterada')]
    public function handlePresentationChanged($data)
    {
        $this->reset(['scores', 'hasVoted']);
        $this->loadCurrentPresentation();
        
        session()->flash('info', 'O palco foi alterado. Nova apresentação carregada.');
    }

    public function loadCurrentPresentation()
    {
        $this->contest->refresh();
        $this->currentPresentation = Presentation::with(['competitor', 'contest.evaluationCriteria'])
            ->find($this->contest->current_presentation_id);
            
        if ($this->currentPresentation) {
            $this->checkIfVoted();
            $this->initializeScores();
        }
    }

    private function checkIfVoted()
    {
        if (!$this->currentPresentation) return;

        $this->hasVoted = \App\Models\PresentationScore::where('presentation_id', $this->currentPresentation->id)
            ->where('juror_id', auth()->id())
            ->exists();
    }

    private function initializeScores()
    {
        $this->scores = [];
        foreach ($this->contest->evaluationCriteria as $criterion) {
            $this->scores[$criterion->id] = 0;
        }
    }

    public function submit()
    {
        if ($this->hasVoted || !$this->currentPresentation) {
            return;
        }

        // Validação Dinâmica
        $rules = [];
        $messages = [];
        foreach ($this->contest->evaluationCriteria as $criterion) {
            $rules["scores.{$criterion->id}"] = "required|numeric|min:0|max:{$criterion->max_score}";
            $messages["scores.{$criterion->id}.max"] = "A nota para '{$criterion->name}' não pode exceder {$criterion->max_score}.";
        }

        $this->validate($rules, $messages);

        try {
            \App\Services\PontuacaoService::run(
                $this->currentPresentation,
                auth()->user(),
                $this->scores
            );

            $this->hasVoted = true;
            $this->dispatch('notify', 'Notas enviadas com sucesso!');
        } catch (\Exception $e) {
            $this->dispatch('notify', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.juror.evaluation-panel');
    }
}
