<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\Presentation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresentationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $competitor;
    protected $contest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $adminRole = Role::where('slug', 'admin')->first();
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);

        $competitorRole = Role::where('slug', 'competidor')->first();
        $this->competitor = User::factory()->create(['role_id' => $competitorRole->id]);

        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Event Test',
            'event_date' => now()->format('Y-m-d'),
        ]);
        $this->contest = Contest::create([
            'event_id' => $event->id,
            'name' => 'Contest Test',
            'status' => 'AGENDADO'
        ]);
    }

    public function test_can_create_presentation_via_api(): void
    {
        $this->actingAs($this->competitor);

        $response = $this->postJson('/api/presentations', [
            'contest_id' => $this->contest->id,
            'work_title' => 'My Performance'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('presentations', [
            'work_title' => 'My Performance',
            'status' => 'EM_ANALISE',
            'competitor_id' => $this->competitor->id
        ]);
    }

    public function test_can_approve_presentation_via_api(): void
    {
        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $this->competitor->id,
            'work_title' => 'Test',
            'status' => 'EM_ANALISE'
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson("/api/presentations/{$presentation->id}/evaluate", [
            'status' => 'APTO'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('APTO', $presentation->fresh()->status);
    }

    public function test_can_reject_presentation_with_justification_via_api(): void
    {
        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $this->competitor->id,
            'work_title' => 'Test',
            'status' => 'EM_ANALISE'
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson("/api/presentations/{$presentation->id}/evaluate", [
            'status' => 'INAPTO',
            'justification_inapto' => 'Documentos incompletos'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('INAPTO', $presentation->fresh()->status);
        $this->assertEquals('Documentos incompletos', $presentation->fresh()->justification_inapto);
    }
}
