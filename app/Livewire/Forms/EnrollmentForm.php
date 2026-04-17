<?php

namespace App\Livewire\Forms;

use App\Services\PresentationService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EnrollmentForm extends Form
{
    #[Validate('required|exists:contests,id')]
    public $contest_id = '';

    #[Validate('required|string|max:255')]
    public $work_title = '';

    public function enroll()
    {
        $this->validate();

        PresentationService::run([
            'contest_id' => $this->contest_id,
            'work_title' => $this->work_title,
        ]);

        $this->reset();
    }
}
