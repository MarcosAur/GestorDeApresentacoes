<?php

namespace App\Livewire\Public;

use App\Models\Contest;
use App\Models\Presentation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class StageViewer extends Component
{
    public Contest $contest;
    public ?Presentation $currentPresentation = null;

    public function mount(Contest $contest)
    {
        $this->contest = $contest;
        $this->loadCurrentPresentation();
    }

    #[On('echo:concurso.{contest.id},ApresentacaoAlterada')]
    public function handlePresentationChanged($data)
    {
        $this->loadCurrentPresentation();
    }

    public function loadCurrentPresentation()
    {
        $this->contest->refresh();
        $this->currentPresentation = Presentation::with(['user'])
            ->find($this->contest->current_presentation_id);
    }

    public function render()
    {
        return view('livewire.public.stage-viewer');
    }
}
