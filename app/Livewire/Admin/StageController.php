<?php

namespace App\Livewire\Admin;

use App\Models\Contest;
use App\Models\Presentation;
use App\Events\ApresentacaoAlterada;
use App\Services\PontuacaoService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class StageController extends Component
{
    public Contest $contest;
    public $presentations = [];
    public $votedJurors = [];

    public function mount(Contest $contest)
    {
        $this->contest = $contest->load(['jurors', 'evaluationCriteria']);
        $this->loadPresentations();
        $this->loadVotedJurors();
    }

    public function loadPresentations()
    {
        $this->presentations = Presentation::where('contest_id', $this->contest->id)
            ->where('status', 'APTO')
            ->where('checkin_realizado', true)
            ->with('competitor')
            ->get();
    }

    public function loadVotedJurors()
    {
        if (!$this->contest->current_presentation_id) {
            $this->votedJurors = [];
            return;
        }

        $votedIds = \App\Models\PresentationScore::where('presentation_id', $this->contest->current_presentation_id)
            ->distinct('juror_id')
            ->pluck('juror_id')
            ->toArray();

        $this->votedJurors = $votedIds;
    }

    public function setOnStage($presentationId)
    {
        // Validar se todos votaram antes de trocar (se houver uma apresentação atual)
        if ($this->contest->current_presentation_id && !PontuacaoService::checkAllJurorsVoted($this->contest)) {
            $this->dispatch('notify', 'Aguardando votos de todos os jurados para trocar.');
            return;
        }

        $this->contest->current_presentation_id = $presentationId;
        
        if ($this->contest->status === 'AGENDADO') {
            $this->contest->status = 'EM_ANDAMENTO';
        }

        $this->contest->save();

        broadcast(new ApresentacaoAlterada($this->contest->id, $presentationId))->toOthers();
        
        $this->loadVotedJurors();
        $this->dispatch('notify', 'Palco atualizado!');
    }

    public function finishContest()
    {
        if ($this->contest->current_presentation_id && !PontuacaoService::checkAllJurorsVoted($this->contest)) {
            $this->dispatch('notify', 'Aguardando votos finais dos jurados.');
            return;
        }

        $this->contest->status = 'FINALIZADO';
        $this->contest->current_presentation_id = null;
        $this->contest->save();

        broadcast(new ApresentacaoAlterada($this->contest->id, null))->toOthers();

        $this->dispatch('notify', 'Concurso Finalizado!');
        return redirect()->route('contests.index');
    }

    public function render()
    {
        return view('livewire.admin.stage-controller');
    }
}
