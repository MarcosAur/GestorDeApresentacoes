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
            'status' => 'APTO',
            'qr_code_hash' => 'test-hash-123'
        ]);

        $this->actingAs($this->admin);

        $response = $this->postJson('/api/checkin', [
            'hash' => 'test-hash-123'
        ]);

        $response->assertStatus(200);
        $this->assertTrue($presentation->fresh()->checkin_realizado, 'Check-in should be marked as realized in the database');
    }

    public function test_checkin_fails_if_not_apto(): void
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

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Apresentação não está apta para check-in.');
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
            'status' => 'APTO',
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

    public function test_presentation_index_returns_qrcode_svg_for_competitor_if_apto(): void
    {
        $competitor = User::factory()->create([
            'role_id' => \App\Models\Role::where('slug', 'competidor')->first()->id
        ]);

        $apto = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $competitor->id,
            'work_title' => 'Apto Work',
            'status' => 'APTO',
            'qr_code_hash' => 'apto-hash',
            'created_at' => now()->subMinutes(5)
        ]);

        $analise = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $competitor->id,
            'work_title' => 'Analise Work',
            'status' => 'EM_ANALISE',
            'qr_code_hash' => 'analise-hash',
            'created_at' => now()
        ]);

        $this->actingAs($competitor);

        $response = $this->getJson('/api/presentations');

        $response->assertStatus(200);
        
        // Como a ordem pode variar, vamos encontrar os itens corretos.
        $aptoData = collect($response->json())->firstWhere('work_title', 'Apto Work');
        $analiseData = collect($response->json())->firstWhere('work_title', 'Analise Work');

        $this->assertNotNull($aptoData, 'Apresentação "Apto Work" não encontrada');
        $this->assertNotNull($analiseData, 'Apresentação "Analise Work" não encontrada');

        $this->assertNull($analiseData['qrcode_svg']);
        
        $qrcode = $aptoData['qrcode_svg'];
        $this->assertStringContainsString('<svg', $qrcode);
        // Verifica se a cor é preta (#000000)
        $this->assertStringContainsString('#000000', $qrcode);
    }
}
