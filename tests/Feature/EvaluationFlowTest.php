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
use Livewire\Livewire;
use Tests\TestCase;

class EvaluationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
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

        $test = Livewire::test(\App\Livewire\Juror\EvaluationPanel::class, ['contest' => $contest])
            ->set('scores.' . $criterion->id, 8.5)
            ->call('submit');
            
        if ($test->errors()->any()) {
            dd($test->errors());
        }

        $test->assertHasNoErrors()
            ->assertSet('hasVoted', true);

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

        Livewire::test(\App\Livewire\Admin\StageController::class, ['contest' => $contest])
            ->call('setOnStage', $p2->id)
            ->assertDispatched('notify', 'Aguardando votos de todos os jurados para trocar.');

        $this->assertEquals($p1->id, $contest->fresh()->current_presentation_id);
    }
}
