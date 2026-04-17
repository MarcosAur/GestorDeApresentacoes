<?php

namespace App\Livewire\Competitor;

use App\Livewire\Forms\EnrollmentForm;
use App\Livewire\Forms\DocumentUploadForm;
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

    public EnrollmentForm $enrollmentForm;
    public DocumentUploadForm $uploadForm;

    public function enroll()
    {
        $this->enrollmentForm->enroll();
        session()->flash('message', 'Inscrição realizada com sucesso!');
    }

    public function uploadDocument()
    {
        $this->uploadForm->upload();
        session()->flash('message', 'Documento enviado com sucesso!');
    }

    public function render()
    {
        $contests = Contest::where('status', 'AGENDADO')
            ->whereDoesntHave('presentations', function ($query) {
                $query->where('competitor_id', Auth::id())
                ->whereNull('deleted_at');
            })->get();

        return view('livewire.competitor.enrollment-panel', [
            'contests' => $contests,
            'presentations' => Auth::user()->presentations()->with('contest')->get(),
            'documents' => Auth::user()->documents()->get(),
        ]);
    }
}
