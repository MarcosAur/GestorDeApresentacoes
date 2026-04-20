<?php

namespace Tests\Feature;

use App\Models\Contest;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $contestReleased;
    protected $contestDraft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'competidor')->first();

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->user = User::factory()->create(['role_id' => $userRole->id]);

        $event = Event::factory()->create();

        $this->contestReleased = Contest::factory()->create([
            'event_id' => $event->id,
            'status' => 'FINALIZADO',
            'ranking_released' => true,
        ]);

        $this->contestDraft = Contest::factory()->create([
            'event_id' => $event->id,
            'status' => 'FINALIZADO',
            'ranking_released' => false,
        ]);
    }

    public function test_admin_can_see_all_finalized_rankings()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/public/rankings');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_regular_user_only_sees_released_rankings()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/public/rankings');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $this->contestReleased->id]);
        $response->assertJsonMissing(['id' => $this->contestDraft->id]);
    }

    public function test_guest_only_sees_released_rankings()
    {
        $response = $this->getJson('/api/public/rankings');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $this->contestReleased->id]);
        $response->assertJsonMissing(['id' => $this->contestDraft->id]);
    }

    public function test_admin_can_access_unreleased_ranking_detail()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/public/contests/{$this->contestDraft->id}/ranking");

        $response->assertStatus(200);
        $response->assertJsonPath('contest.id', $this->contestDraft->id);
    }

    public function test_regular_user_cannot_access_unreleased_ranking_detail()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/public/contests/{$this->contestDraft->id}/ranking");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_unreleased_ranking_detail()
    {
        $response = $this->getJson("/api/public/contests/{$this->contestDraft->id}/ranking");

        $response->assertStatus(403);
    }
}
