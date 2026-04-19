<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Presentation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Bugfix1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
    }

    public function test_presentation_status_changes_to_finalizada_when_removed_from_stage()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $contest = Contest::factory()->create();
        
        $p1 = Presentation::factory()->create(['contest_id' => $contest->id, 'status' => 'APTO', 'checkin_realizado' => true]);
        $p2 = Presentation::factory()->create(['contest_id' => $contest->id, 'status' => 'APTO', 'checkin_realizado' => true]);

        $contest->update(['current_presentation_id' => $p1->id, 'status' => 'EM_ANDAMENTO']);

        $this->actingAs($admin);

        $response = $this->postJson("/api/contests/{$contest->id}/stage", [
            'presentation_id' => $p2->id
        ]);

        $response->assertStatus(200);
        $this->assertEquals('FINALIZADA', $p1->fresh()->status);
        $this->assertEquals($p2->id, $contest->fresh()->current_presentation_id);
    }

    public function test_presentation_status_changes_to_finalizada_when_contest_ends()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $contest = Contest::factory()->create();
        
        $p1 = Presentation::factory()->create(['contest_id' => $contest->id, 'status' => 'APTO', 'checkin_realizado' => true]);

        $contest->update(['current_presentation_id' => $p1->id, 'status' => 'EM_ANDAMENTO']);

        $this->actingAs($admin);

        $response = $this->postJson("/api/contests/{$contest->id}/finish");

        $response->assertStatus(200);
        $this->assertEquals('FINALIZADA', $p1->fresh()->status);
        $this->assertEquals('FINALIZADO', $contest->fresh()->status);
    }

    public function test_stage_api_lists_apto_presentations_regardless_of_checkin()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $contest = Contest::factory()->create();
        
        $p1 = Presentation::factory()->create([
            'contest_id' => $contest->id, 
            'status' => 'APTO', 
            'checkin_realizado' => false,
            'work_title' => 'Trabalho Offline'
        ]);

        $this->actingAs($admin);

        $response = $this->getJson("/api/contests/{$contest->id}/stage");
        
        $response->assertStatus(200);
        $response->assertJsonFragment(['work_title' => 'Trabalho Offline']);
    }
}
