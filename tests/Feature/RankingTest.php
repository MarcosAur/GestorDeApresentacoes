<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\Presentation;
use App\Models\User;
use App\Models\Role;
use App\Models\EvaluationCriterion;
use App\Models\PresentationScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $contest;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Role::create(['name' => 'Jurado', 'slug' => 'jurado']);
        
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        
        $event = Event::factory()->create(['admin_id' => $this->admin->id]);
        $this->contest = Contest::factory()->create(['event_id' => $event->id]);
    }

    public function test_admin_can_access_ranking_at_any_time()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/contests/{$this->contest->id}/ranking/admin");

        $response->assertStatus(200)
            ->assertJsonStructure(['contest', 'ranking']);
    }

    public function test_public_cannot_access_ranking_before_finalization()
    {
        $this->contest->update(['status' => 'EM_ANDAMENTO']);

        $response = $this->getJson("/api/public/contests/{$this->contest->id}/ranking");

        $response->assertStatus(403)
            ->assertJson(['message' => 'O ranking estará disponível após a finalização do concurso.']);
    }

    public function test_public_can_access_ranking_after_finalization()
    {
        $this->contest->update(['status' => 'FINALIZADO']);

        $response = $this->getJson("/api/public/contests/{$this->contest->id}/ranking");

        $response->assertStatus(200)
            ->assertJsonStructure(['contest', 'ranking']);
    }

    public function test_ranking_calculation_with_scores()
    {
        // Criar critério
        $criterion = EvaluationCriterion::create([
            'contest_id' => $this->contest->id,
            'name' => 'Originalidade',
            'max_score' => 10,
            'weight' => 2,
            'tiebreak_priority' => 1
        ]);

        $competitorRole = Role::create(['name' => 'Competidor', 'slug' => 'competidor']);
        $user1 = User::factory()->create(['role_id' => $competitorRole->id]);
        $user2 = User::factory()->create(['role_id' => $competitorRole->id]);

        $pres1 = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $user1->id,
            'work_title' => 'Show A',
            'status' => 'APTO',
            'checkin_realizado' => true
        ]);

        $pres2 = Presentation::create([
            'contest_id' => $this->contest->id,
            'competitor_id' => $user2->id,
            'work_title' => 'Show B',
            'status' => 'APTO',
            'checkin_realizado' => true
        ]);

        $juror = User::factory()->create(['role_id' => Role::where('slug', 'jurado')->first()->id]);

        // Pres 1: Nota 8 * Peso 2 = 16
        PresentationScore::create([
            'presentation_id' => $pres1->id,
            'juror_id' => $juror->id,
            'criterion_id' => $criterion->id,
            'assigned_score' => 8
        ]);

        // Pres 2: Nota 9 * Peso 2 = 18
        PresentationScore::create([
            'presentation_id' => $pres2->id,
            'juror_id' => $juror->id,
            'criterion_id' => $criterion->id,
            'assigned_score' => 9
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/contests/{$this->contest->id}/ranking/admin");

        $response->assertStatus(200);
        $ranking = $response->json('ranking');

        $this->assertEquals($pres2->id, $ranking[0]['presentation']['id']);
        $this->assertEquals(18, $ranking[0]['total_score']);
        $this->assertEquals($pres1->id, $ranking[1]['presentation']['id']);
        $this->assertEquals(16, $ranking[1]['total_score']);
    }
}
