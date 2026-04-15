<?php

namespace App\Livewire;

use App\Models\Contest;
use App\Models\Event;
use App\Models\User;
use App\Models\EvaluationCriterion;
use Livewire\Component;
use Livewire\WithPagination;

class ContestManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingContest = null;

    public $event_id;
    public $name;
    public $status = 'AGENDADO';

    // Jurors and Criteria management
    public $selectedJurors = [];
    public $criteria = [];

    protected $rules = [
        'event_id' => 'required|exists:events,id',
        'name' => 'required|string|max:255',
        'status' => 'required|in:AGENDADO,EM_ANDAMENTO,FINALIZADO',
        'selectedJurors' => 'array',
        'selectedJurors.*' => 'exists:users,id',
        'criteria.*.name' => 'required|string|max:255',
        'criteria.*.max_score' => 'required|numeric|min:0',
        'criteria.*.weight' => 'required|numeric|min:0',
        'criteria.*.tiebreak_priority' => 'nullable|integer',
    ];

    public function openModal($contestId = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'status', 'criteria', 'selectedJurors']);

        if ($contestId) {
            $this->editingContest = Contest::with(['evaluationCriteria', 'jurors'])->findOrFail($contestId);
            $this->event_id = $this->editingContest->event_id;
            $this->name = $this->editingContest->name;
            $this->status = $this->editingContest->status;
            $this->criteria = $this->editingContest->evaluationCriteria->toArray();
            $this->selectedJurors = $this->editingContest->jurors->pluck('id')->toArray();
        } else {
            $this->editingContest = null;
            $this->criteria = [];
            $this->selectedJurors = [];
        }

        $this->showModal = true;
    }

    public function addCriterion()
    {
        $this->criteria[] = [
            'name' => '',
            'max_score' => 10,
            'weight' => 1,
            'tiebreak_priority' => count($this->criteria) + 1,
        ];
    }

    public function removeCriterion($index)
    {
        unset($this->criteria[$index]);
        $this->criteria = array_values($this->criteria);
    }

    public function save()
    {
        $this->validate();

        // Check for duplicate tiebreak priorities
        $priorities = collect($this->criteria)->pluck('tiebreak_priority')->filter()->toArray();
        if (count($priorities) !== count(array_unique($priorities))) {
            $this->addError('criteria_tiebreak', 'Não é permitido repetir a mesma prioridade de desempate em critérios diferentes.');
            return;
        }

        if ($this->editingContest) {
            $this->editingContest->update([
                'event_id' => $this->event_id,
                'name' => $this->name,
                'status' => $this->status,
            ]);

            // Sync jurors
            $this->editingContest->jurors()->sync($this->selectedJurors);

            // Sync criteria
            $this->editingContest->evaluationCriteria()->delete();
            foreach ($this->criteria as $criterionData) {
                $this->editingContest->evaluationCriteria()->create($criterionData);
            }

            $this->dispatch('notify', 'Concurso, jurados e critérios atualizados!');
        } else {
            $contest = Contest::create([
                'event_id' => $this->event_id,
                'name' => $this->name,
                'status' => $this->status,
            ]);

            $contest->jurors()->sync($this->selectedJurors);

            foreach ($this->criteria as $criterionData) {
                $contest->evaluationCriteria()->create($criterionData);
            }

            $this->dispatch('notify', 'Concurso criado com sucesso!');
        }

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
