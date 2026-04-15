<?php

namespace App\Livewire\Competitor;

use App\Models\Contest;
use App\Models\Presentation;
use App\Services\DocumentService;
use App\Services\PresentationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class EnrollmentPanel extends Component
{
    use WithFileUploads;

    public $contest_id;
    public $work_title;
    public $document_file;
    public $document_type;

    protected $rules = [
        'contest_id' => 'required|exists:contests,id',
        'work_title' => 'required|string|max:255',
    ];

    public function enroll()
    {
        $this->validate();

        PresentationService::run([
            'contest_id' => $this->contest_id,
            'work_title' => $this->work_title,
        ]);

        $this->reset(['contest_id', 'work_title']);
        session()->flash('message', 'Inscrição realizada com sucesso!');
    }

    public function uploadDocument()
    {
        $this->validate([
            'document_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'document_type' => 'required|string|max:255',
        ]);

        DocumentService::run(Auth::user(), $this->document_file, $this->document_type);

        $this->reset(['document_file', 'document_type']);
        session()->flash('message', 'Documento enviado com sucesso!');
    }

    public function render()
    {
        return view('livewire.competitor.enrollment-panel', [
            'contests' => Contest::where('status', 'AGENDADO')->get(),
            'presentations' => Auth::user()->presentations()->with('contest')->get(),
            'documents' => Auth::user()->documents()->get(),
        ]);
    }
}
