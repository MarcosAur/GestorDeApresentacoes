<?php

namespace App\Livewire;

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

    public $showModal = false;
    public $editingJuror = null;

    public $name;
    public $email;
    public $password;
    public $selectedContests = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'selectedContests' => 'array',
        'selectedContests.*' => 'exists:contests,id',
    ];

    public function openModal($jurorId = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'selectedContests']);

        if ($jurorId) {
            $this->editingJuror = User::with('contests')->findOrFail($jurorId);
            $this->name = $this->editingJuror->name;
            $this->email = $this->editingJuror->email;
            $this->selectedContests = $this->editingJuror->contests->pluck('id')->toArray();
        } else {
            $this->editingJuror = null;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingJuror) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->editingJuror->id;
        } else {
            $rules['password'] = 'required|min:8';
        }

        $this->validate($rules);

        $jurorRole = Role::where('slug', 'jurado')->first();

        if ($this->editingJuror) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->editingJuror->update($data);
            $this->editingJuror->contests()->sync($this->selectedContests);
            $this->dispatch('notify', 'Jurado atualizado com sucesso!');
        } else {
            $juror = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role_id' => $jurorRole->id,
            ]);

            $juror->contests()->sync($this->selectedContests);
            $this->dispatch('notify', 'Jurado cadastrado e vinculado!');
        }

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
