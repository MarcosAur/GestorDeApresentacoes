<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\Presentation;
use App\Models\User;
use App\Services\PresentationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $contest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = User::factory()->create([
            'role_id' => \App\Models\Role::where('slug', 'admin')->first()->id
        ]);

        $event = Event::create([
            'admin_id' => $this->admin->id,
            'name' => 'Event Test',
            'event_date' => now()->addDays(10),
            'description' => 'Test description'
        ]);

        $this->contest = Contest::create([
            'event_id' => $event->id,
            'name' => 'Contest Test',
            'status' => 'AGENDADO'
        ]);
    }

    public function test_presentation_creation_generates_qr_code_hash(): void
    {
        $competitor = User::factory()->create([
            'role_id' => \App\Models\Role::where('slug', 'competidor')->first()->id
        ]);

        $this->actingAs($competitor);

        $presentation = PresentationService::run([
            'contest_id' => $this->contest->id,
            'work_title' => 'Test Work'
        ]);

        $this->assertNotNull($presentation->qr_code_hash);
        $this->assertIsString($presentation->qr_code_hash);
        $this->assertNotEmpty($presentation->qr_code_hash);
    }

    public function test_admin_can_perform_checkin_via_api(): void
    {
        $competitor = User::factory()->create([
            'role_id' => \App\Models\Role::where('slug', 'competidor')->first()->id
        ]);

        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $competitor->id,
            'work_title' => 'Test Work',
            'status' => 'EM_ANALISE',
            'qr_code_hash' => 'test-hash-123'
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/checkin', [
            'hash' => 'test-hash-123'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($presentation->fresh()->checkin_realizado, 'Check-in should be marked as realized in the database');
    }

    public function test_checkin_fails_with_invalid_hash(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/api/checkin', [
            'hash' => 'invalid-hash'
        ]);

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'QR Code inválido ou não encontrado.');
    }

    public function test_checkin_fails_if_already_performed(): void
    {
        $competitor = User::factory()->create([
            'role_id' => \App\Models\Role::where('slug', 'competidor')->first()->id
        ]);

        $presentation = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $competitor->id,
            'work_title' => 'Test Work',
            'status' => 'EM_ANALISE',
            'qr_code_hash' => 'test-hash-123',
            'checkin_realizado' => true
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/checkin', [
            'hash' => 'test-hash-123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', "Check-in já realizado para: {$competitor->name}");
    }
}
