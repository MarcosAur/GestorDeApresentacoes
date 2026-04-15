<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\Contest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

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
    }

    public function test_can_create_event(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\EventList::class)
            ->set('name', 'Evento de Teste')
            ->set('event_date', '2026-12-31')
            ->set('description', 'Uma descrição teste')
            ->call('save');

        $this->assertTrue(Event::where('name', 'Evento de Teste')->exists());
    }

    public function test_can_edit_event(): void
    {
        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Evento Original',
            'event_date' => '2026-01-01',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\EventList::class)
            ->call('openModal', $event->id)
            ->set('name', 'Evento Atualizado')
            ->call('save');

        $this->assertEquals('Evento Atualizado', $event->fresh()->name);
    }

    public function test_cannot_delete_event_with_contests(): void
    {
        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Evento com Concurso',
            'event_date' => '2026-01-01',
        ]);

        Contest::create([
            'event_id' => $event->id,
            'name' => 'Concurso 1',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\EventList::class)
            ->call('delete', $event->id);

        $this->assertTrue(Event::where('id', $event->id)->exists());
    }

    public function test_can_delete_event_without_contests(): void
    {
        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Evento Vazio',
            'event_date' => '2026-01-01',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\EventList::class)
            ->call('delete', $event->id);

        $this->assertSoftDeleted($event);
    }
}
