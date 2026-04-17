<?php

namespace App\Livewire\Forms;

use App\Models\Contest;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContestForm extends Form
{
    public ?Contest $contest = null;

    #[Validate('required|exists:events,id')]
    public $event_id = '';

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|in:AGENDADO,EM_ANDAMENTO,FINALIZADO')]
    public $status = 'AGENDADO';

    #[Validate([
        'selectedJurors' => 'array',
        'selectedJurors.*' => 'exists:users,id',
    ])]
    public $selectedJurors = [];

    #[Validate([
        'criteria.*.name' => 'required|string|max:255',
        'criteria.*.max_score' => 'required|numeric|min:0',
        'criteria.*.weight' => 'required|numeric|min:0',
        'criteria.*.tiebreak_priority' => 'nullable|integer',
    ])]
    public $criteria = [];

    public function setContest(Contest $contest)
    {
        $this->contest = $contest;
        $this->event_id = $contest->event_id;
        $this->name = $contest->name;
        $this->status = $contest->status;
        $this->criteria = $contest->evaluationCriteria->toArray();
        $this->selectedJurors = $contest->jurors->pluck('id')->toArray();
    }

    public function save()
    {
        $this->validate();

        // Check for duplicate tiebreak priorities
        $priorities = collect($this->criteria)->pluck('tiebreak_priority')->filter()->toArray();
        if (count($priorities) !== count(array_unique($priorities))) {
            return ['error' => 'Não é permitido repetir a mesma prioridade de desempate em critérios diferentes.'];
        }

        if ($this->contest) {
            $this->contest->update([
                'event_id' => $this->event_id,
                'name' => $this->name,
                'status' => $this->status,
            ]);

            $this->contest->jurors()->sync($this->selectedJurors);
            $this->contest->evaluationCriteria()->delete();
            foreach ($this->criteria as $criterionData) {
                // Remove ID if present from toArray() to avoid issues with mass assignment or primary keys
                unset($criterionData['id'], $criterionData['contest_id'], $criterionData['created_at'], $criterionData['updated_at'], $criterionData['deleted_at']);
                $this->contest->evaluationCriteria()->create($criterionData);
            }

            return ['success' => 'Concurso atualizado com sucesso!'];
        }

        $contest = Contest::create([
            'event_id' => $this->event_id,
            'name' => $this->name,
            'status' => $this->status,
        ]);

        $contest->jurors()->sync($this->selectedJurors);
        foreach ($this->criteria as $criterionData) {
            $contest->evaluationCriteria()->create($criterionData);
        }

        return ['success' => 'Concurso criado com sucesso!'];
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
}
