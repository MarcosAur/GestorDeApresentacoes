<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurorCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $contest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $role = Role::where('slug', 'admin')->first();
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Evento Teste',
            'event_date' => '2026-01-01',
        ]);

        $this->contest = Contest::create([
            'event_id' => $event->id,
            'name' => 'Concurso Teste',
        ]);
    }

    public function test_can_create_juror_and_link_to_contest(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/api/jurors', [
            'name' => 'Jurado K-Pop',
            'email' => 'jurado@test.com',
            'password' => 'password123',
            'selectedContests' => [$this->contest->id],
        ]);

        $response->assertStatus(201);
        $juror = User::where('email', 'jurado@test.com')->first();
        $this->assertNotNull($juror);
        $this->assertTrue($juror->hasRole('jurado'));
        $this->assertCount(1, $juror->contests);
        $this->assertEquals($this->contest->id, $juror->contests->first()->id);
    }

    public function test_can_update_juror_vincule(): void
    {
        $role = Role::where('slug', 'jurado')->first();
        $juror = User::create([
            'name' => 'Jurado Antigo',
            'email' => 'antigo@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->putJson("/api/jurors/{$juror->id}", [
            'name' => 'Jurado Atualizado',
            'email' => 'antigo@test.com',
            'selectedContests' => [$this->contest->id],
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $juror->fresh()->contests);
    }
}
