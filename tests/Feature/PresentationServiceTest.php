<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\Presentation;
use App\Models\Role;
use App\Models\User;
use App\Services\PresentationService;
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

    public function test_can_create_presentation(): void
    {
        $this->actingAs($this->competitor);

        $data = [
            'contest_id' => $this->contest->id,
            'work_title' => 'My Performance'
        ];

        $presentation = PresentationService::run($data);

        $this->assertInstanceOf(Presentation::class, $presentation);
        $this->assertEquals('My Performance', $presentation->work_title);
        $this->assertEquals('EM_ANALISE', $presentation->status);
        $this->assertEquals($this->competitor->id, $presentation->competitor_id);
    }

    public function test_can_approve_presentation(): void
    {
        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $this->competitor->id,
            'work_title' => 'Test',
            'status' => 'EM_ANALISE'
        ]);

        $data = [
            'status' => 'APTO'
        ];

        PresentationService::evaluate($presentation, $data);

        $this->assertEquals('APTO', $presentation->fresh()->status);
        $this->assertNull($presentation->fresh()->justification_inapto);
    }

    public function test_can_reject_presentation_with_justification(): void
    {
        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $this->competitor->id,
            'work_title' => 'Test',
            'status' => 'EM_ANALISE'
        ]);

        $data = [
            'status' => 'INAPTO',
            'justification_inapto' => 'Documentos incompletos'
        ];

        PresentationService::evaluate($presentation, $data);

        $this->assertEquals('INAPTO', $presentation->fresh()->status);
        $this->assertEquals('Documentos incompletos', $presentation->fresh()->justification_inapto);
    }
}
