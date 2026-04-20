<?php

namespace Tests\Feature;

use App\Events\ApresentacaoAlterada;
use App\Models\Contest;
use App\Models\Presentation;
use App\Models\Role;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event as EventFacade;
use Tests\TestCase;

class ReproductionSetStageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
    }

    public function test_apresentacao_alterada_is_dispatched_on_set_on_stage()
    {
        EventFacade::fake();

        $admin = User::factory()->create([
            'role_id' => Role::where('slug', 'admin')->first()->id
        ]);
        
        $event = Event::factory()->create();
        $contest = Contest::factory()->create(['event_id' => $event->id]);
        
        $presentation = Presentation::factory()->create([
            'contest_id' => $contest->id,
            'checkin_realizado' => true,
            'status' => 'APTO'
        ]);

        $response = $this->actingAs($admin)
            ->postJson("/api/contests/{$contest->id}/stage", [
                'presentation_id' => $presentation->id
            ]);

        $response->assertStatus(200);
        
        EventFacade::assertDispatched(ApresentacaoAlterada::class);
    }
}
