<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\EvaluationCriterion;
use App\Models\Presentation;
use App\Models\Role;
use App\Models\User;
use App\Services\PontuacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Events\NotaAtribuida;
use App\Events\ApresentacaoAlterada;
use Illuminate\Support\Facades\Event as EventFacade;

class EvaluationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        EventFacade::fake([
            NotaAtribuida::class,
            ApresentacaoAlterada::class,
        ]);
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Role::create(['name' => 'Jurado', 'slug' => 'jurado']);
    }

    public function test_juror_can_submit_scores_only_for_active_presentation()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $juror = User::factory()->create(['role_id' => Role::where('slug', 'jurado')->first()->id]);
        
        $event = Event::factory()->create(['admin_id' => $admin->id]);
        $contest = Contest::factory()->create(['event_id' => $event->id, 'status' => 'EM_ANDAMENTO']);
        $contest->jurors()->attach($juror->id);
        
        $criterion = EvaluationCriterion::create([
            'contest_id' => $contest->id,
            'name' => 'Canto',
            'max_score' => 10,
            'weight' => 2
        ]);

        $presentation = Presentation::factory()->create([
            'contest_id' => $contest->id,
            'status' => 'APTO',
            'checkin_realizado' => true
        ]);

        $contest->update(['current_presentation_id' => $presentation->id]);

        $this->actingAs($juror);

        $response = $this->postJson("/api/contests/{$contest->id}/evaluation", [
            'scores' => [
                $criterion->id => 8.5
            ]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('presentation_scores', [
            'presentation_id' => $presentation->id,
            'juror_id' => $juror->id,
            'assigned_score' => 8.5
        ]);
    }

    public function test_admin_cannot_change_stage_if_jurors_have_not_voted()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $juror = User::factory()->create(['role_id' => Role::where('slug', 'jurado')->first()->id]);
        
        $event = Event::factory()->create(['admin_id' => $admin->id]);
        $contest = Contest::factory()->create(['event_id' => $event->id, 'status' => 'EM_ANDAMENTO']);
        $contest->jurors()->attach($juror->id);

        $p1 = Presentation::factory()->create(['contest_id' => $contest->id, 'status' => 'APTO', 'checkin_realizado' => true]);
        $p2 = Presentation::factory()->create(['contest_id' => $contest->id, 'status' => 'APTO', 'checkin_realizado' => true]);

        $contest->update(['current_presentation_id' => $p1->id]);

        $this->actingAs($admin);

        $response = $this->postJson("/api/contests/{$contest->id}/stage", [
            'presentation_id' => $p2->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Aguardando votos de todos os jurados para trocar.');

        $this->assertEquals($p1->id, $contest->fresh()->current_presentation_id);
    }

    public function test_juror_cannot_access_finalized_contest_evaluation()
    {
        $juror = User::factory()->create(['role_id' => Role::where('slug', 'jurado')->first()->id]);
        $contest = Contest::factory()->create(['status' => 'FINALIZADO']);
        $contest->jurors()->attach($juror->id);

        $this->actingAs($juror);

        $response = $this->getJson("/api/contests/{$contest->id}/evaluation");

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'O concurso já foi finalizado. Avaliação encerrada.');
    }

    public function test_admin_cannot_set_presentation_on_stage_without_checkin()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $event = Event::factory()->create(['admin_id' => $admin->id]);
        $contest = Contest::factory()->create(['event_id' => $event->id]);
        
        $presentation = Presentation::factory()->create([
            'contest_id' => $contest->id,
            'checkin_realizado' => false
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/api/contests/{$contest->id}/stage", [
            'presentation_id' => $presentation->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'O competidor deve realizar o check-in antes de ir para o palco.');
    }
}
