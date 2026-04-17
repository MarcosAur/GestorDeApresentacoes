<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();
        
        $this->command->info('--------------------------------------------');
        $this->command->info('Usuários criados com sucesso:');
        $this->command->info('--------------------------------------------');

        foreach ($roles as $role) {
            $password = '12345678';
            $email = strtolower($role->slug) . '@example.com';
            
            $user = User::create([
                'name' => $role->name . ' User',
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $role->id,
            ]);

            $this->command->info("Role: {$role->name}");
            $this->command->info("Email: {$email}");
            $this->command->info("Senha: {$password}");
            $this->command->info('--------------------------------------------');
        }
    }
}
