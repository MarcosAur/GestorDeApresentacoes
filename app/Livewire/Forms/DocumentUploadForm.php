<?php

namespace App\Livewire\Forms;

use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

class DocumentUploadForm extends Form
{
    use WithFileUploads;

    #[Validate('required|file|mimes:pdf,png,jpg,jpeg|max:5120')]
    public $document_file;

    #[Validate('required|string|max:255')]
    public $document_type = '';

    public function upload()
    {
        $this->validate();

        DocumentService::run(Auth::user(), $this->document_file, $this->document_type);

        $this->reset();
    }
}
