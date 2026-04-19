<?php

namespace Tests\Feature;

use App\Events\ApresentacaoAlterada;
use App\Models\Contest;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event as EventFacade;
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

    public function test_public_stage_api_returns_correct_data()
    {
        $contest = Contest::factory()->create();

        $response = $this->getJson("/api/public/contests/{$contest->id}/stage");

        $response->assertStatus(200);
        $response->assertJsonPath('contest.id', $contest->id);
    }

    public function test_juror_evaluation_api_returns_correct_data()
    {
        $juror = User::factory()->create([
            'role_id' => Role::where('slug', 'jurado')->first()->id
        ]);

        $contest = Contest::factory()->create();
        $contest->jurors()->attach($juror->id);

        $this->actingAs($juror);

        $response = $this->getJson("/api/contests/{$contest->id}/evaluation");

        $response->assertStatus(200);
        $response->assertJsonPath('contest.id', $contest->id);
    }
}
