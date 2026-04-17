<?php

namespace App\Livewire;

use App\Livewire\Forms\JurorForm;
use App\Models\User;
use App\Models\Role;
use App\Models\Contest;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JurorManager extends Component
{
    use WithPagination;

    public JurorForm $form;
    public $showModal = false;
    public $editingJuror = null;

    public function openModal($jurorId = null)
    {
        $this->resetValidation();
        $this->form->reset();

        if ($jurorId) {
            $this->editingJuror = User::with('contests')->findOrFail($jurorId);
            $this->form->setJuror($this->editingJuror);
        } else {
            $this->editingJuror = null;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $message = $this->form->save();
        $this->dispatch('notify', $message);
        $this->showModal = false;
    }

    public function delete($jurorId)
    {
        $juror = User::findOrFail($jurorId);
        // Check for dependencies if necessary (e.g. given scores)
        $juror->delete();
        $this->dispatch('notify', 'Jurado removido com sucesso!');
    }

    public function render()
    {
        return view('livewire.juror-manager', [
            'jurors' => User::whereHas('role', function($q) { $q->where('slug', 'jurado'); })
                        ->with('contests')
                        ->latest()
                        ->paginate(10),
            'contests' => Contest::orderBy('name')->get()
        ]);
    }
}
