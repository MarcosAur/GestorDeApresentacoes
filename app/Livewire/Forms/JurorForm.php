<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;

class JurorForm extends Form
{
    public ?User $user = null;

    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedContests = [];

    public function setJuror(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedContests = $user->contests->pluck('id')->toArray();
        $this->password = '';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->user?->id ?? 'NULL'),
            'password' => $this->user ? 'nullable|min:8' : 'required|min:8',
            'selectedContests' => 'array',
            'selectedContests.*' => 'exists:contests,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $jurorRole = Role::where('slug', 'jurado')->first();

        if ($this->user) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->user->update($data);
            $this->user->contests()->sync($this->selectedContests);
            return 'Jurado atualizado com sucesso!';
        }

        $juror = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $jurorRole->id,
        ]);

        $juror->contests()->sync($this->selectedContests);
        return 'Jurado cadastrado e vinculado!';
    }
}
