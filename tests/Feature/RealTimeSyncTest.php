<?php

namespace Tests\Feature;

use App\Events\ApresentacaoAlterada;
use App\Models\Contest;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event as EventFacade;
use Livewire\Livewire;
use Tests\TestCase;

class RealTimeSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Role::create(['name' => 'Jurado', 'slug' => 'jurado']);
    }

    public function test_apresentacao_alterada_event_can_be_dispatched()
    {
        EventFacade::fake();

        $contest = Contest::factory()->create();

        event(new ApresentacaoAlterada($contest->id, 1));

        EventFacade::assertDispatched(ApresentacaoAlterada::class, function ($event) use ($contest) {
            return $event->contestId === $contest->id && $event->presentationId === 1;
        });
    }

    public function test_stage_viewer_renders_correctly()
    {
        $contest = Contest::factory()->create();

        Livewire::test(\App\Livewire\Public\StageViewer::class, ['contest' => $contest])
            ->assertStatus(200)
            ->assertSet('contest.id', $contest->id);
    }

    public function test_juror_evaluation_panel_renders_correctly()
    {
        $juror = User::factory()->create([
            'role_id' => Role::where('slug', 'jurado')->first()->id
        ]);

        $contest = Contest::factory()->create();
        $contest->jurors()->attach($juror->id);

        $this->actingAs($juror);

        Livewire::test(\App\Livewire\Juror\EvaluationPanel::class, ['contest' => $contest])
            ->assertStatus(200)
            ->assertSet('contest.id', $contest->id);
    }
}
