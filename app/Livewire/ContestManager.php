<?php

namespace App\Livewire;

use App\Livewire\Forms\ContestForm;
use App\Models\Contest;
use App\Models\Event;
use App\Models\User;
use App\Models\EvaluationCriterion;
use Livewire\Component;
use Livewire\WithPagination;

class ContestManager extends Component
{
    use WithPagination;

    public ContestForm $form;
    public $showModal = false;
    public $editingContest = null;

    public function openModal($contestId = null)
    {
        $this->resetValidation();
        $this->form->reset();

        if ($contestId) {
            $this->editingContest = Contest::with(['evaluationCriteria', 'jurors'])->findOrFail($contestId);
            $this->form->setContest($this->editingContest);
        } else {
            $this->editingContest = null;
        }

        $this->showModal = true;
    }

    public function addCriterion()
    {
        $this->form->addCriterion();
    }

    public function removeCriterion($index)
    {
        $this->form->removeCriterion($index);
    }

    public function save()
    {
        $result = $this->form->save();

        if (isset($result['error'])) {
            $this->addError('criteria_tiebreak', $result['error']);
            return;
        }

        $this->dispatch('notify', $result['success']);
        $this->showModal = false;
    }

    public function delete($contestId)
    {
        $contest = Contest::findOrFail($contestId);
        $contest->delete();
        $this->dispatch('notify', 'Concurso removido!');
    }

    public function render()
    {
        return view('livewire.contest-manager', [
            'contests' => Contest::with(['event', 'jurors'])->latest()->paginate(10),
            'events' => Event::orderBy('name')->get(),
            'availableJurors' => User::whereHas('role', function($q) { $q->where('slug', 'jurado'); })->orderBy('name')->get()
        ]);
    }
}
