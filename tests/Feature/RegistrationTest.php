<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_registration_endpoint_works(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test Competitor',
            'email' => 'competitor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        
        $user = User::where('email', 'competitor@example.com')->first();
        $this->assertNotNull($user, 'User should be created in the database');
        $this->assertEquals('competidor', $user->role->slug);

        $this->assertAuthenticated();
        $response->assertJsonPath('user.email', 'competitor@example.com');
    }
}
