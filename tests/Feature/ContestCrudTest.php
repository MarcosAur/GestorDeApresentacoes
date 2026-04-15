<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContestCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $event;

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

        $this->event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Evento Teste',
            'event_date' => '2026-01-01',
        ]);
    }

    public function test_can_create_contest_with_criteria(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\ContestManager::class)
            ->set('event_id', $this->event->id)
            ->set('name', 'Concurso K-Pop')
            ->set('criteria', [
                ['name' => 'Originalidade', 'max_score' => 10, 'weight' => 2, 'tiebreak_priority' => 1]
            ])
            ->call('save');

        $contest = Contest::where('name', 'Concurso K-Pop')->first();
        $this->assertNotNull($contest);
        $this->assertCount(1, $contest->evaluationCriteria);
        $this->assertEquals('Originalidade', $contest->evaluationCriteria->first()->name);
    }

    public function test_can_edit_contest_criteria(): void
    {
        $contest = Contest::create([
            'event_id' => $this->event->id,
            'name' => 'Concurso Antigo',
        ]);
        
        $contest->evaluationCriteria()->create([
            'name' => 'Critério 1',
            'max_score' => 10,
            'weight' => 1
        ]);

        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\ContestManager::class)
            ->call('openModal', $contest->id)
            ->set('criteria.0.name', 'Critério Editado')
            ->call('save');

        $this->assertEquals('Critério Editado', $contest->fresh()->evaluationCriteria->first()->name);
    }

    public function test_cannot_have_duplicate_tiebreak_priority(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\ContestManager::class)
            ->set('event_id', $this->event->id)
            ->set('name', 'Concurso K-Pop')
            ->set('criteria', [
                ['name' => 'Critério 1', 'max_score' => 10, 'weight' => 1, 'tiebreak_priority' => 1],
                ['name' => 'Critério 2', 'max_score' => 10, 'weight' => 1, 'tiebreak_priority' => 1]
            ])
            ->call('save')
            ->assertHasErrors(['criteria_tiebreak']);
    }
}
