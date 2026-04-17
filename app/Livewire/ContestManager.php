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
        if (!auth()->user()->hasRole('admin')) {
            return;
        }

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
        if (!auth()->user()->hasRole('admin')) {
            return;
        }

        $this->form->addCriterion();
    }

    public function removeCriterion($index)
    {
        if (!auth()->user()->hasRole('admin')) {
            return;
        }

        $this->form->removeCriterion($index);
    }

    public function save()
    {
        if (!auth()->user()->hasRole('admin')) {
            return;
        }

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
        if (!auth()->user()->hasRole('admin')) {
            return;
        }

        $contest = Contest::findOrFail($contestId);
        $contest->delete();
        $this->dispatch('notify', 'Concurso removido!');
    }

    public function render()
    {
        $query = Contest::with(['event', 'jurors'])->latest();

        if (auth()->user()->hasRole('jurado') && !auth()->user()->hasRole('admin')) {
            $query->whereHas('jurors', function($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return view('livewire.contest-manager', [
            'contests' => $query->paginate(10),
            'events' => Event::orderBy('name')->get(),
            'availableJurors' => User::whereHas('role', function($q) { $q->where('slug', 'jurado'); })->orderBy('name')->get()
        ]);
    }
}
