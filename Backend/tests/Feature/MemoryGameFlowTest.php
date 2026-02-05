<?php

namespace Tests\Feature;

use App\Models\GameScore;
use App\Models\User;
use App\Models\Villager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoryGameFlowTest extends TestCase
{
    use RefreshDatabase;

    private function seedVillagers(int $count = 25): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Villager::create([
                'name_es' => "Aldeano {$i}",
                'name_en' => "Villager {$i}",
                'file_name' => "villager_{$i}",
                'personality' => 'Normal',
                'species' => 'Cat',
                'gender' => 'Male',
                'hobby' => 'Play',
                'birthday' => '1/1',
                'birthday_string' => 'January 1st',
            ]);
        }
    }

    public function test_memory_game_returns_correct_payload(): void
    {
        $this->seedVillagers();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/games/memory/1/data');

        $response->assertOk()
            ->assertJsonStructure([
                'level',
                'pairs',
                'cards',
                'bestTime',
                'bestMoves',
                'memorizeSeconds',
            ]);

        $this->assertEquals(1, $response->json('level'));
        $this->assertEquals(9, $response->json('pairs'));
        $this->assertCount(18, $response->json('cards')); // 9 pairs * 2
    }

    public function test_score_can_be_saved(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/games/memory/score', [
                'level' => 1,
                'time_seconds' => 45.5,
                'moves' => 20,
            ]);

        $response->assertOk()
            ->assertJson(['updated' => true]);

        $this->assertDatabaseHas('game_scores', [
            'user_id' => $user->id,
            'level' => 1,
            'moves' => 20,
        ]);
    }

    public function test_better_score_updates_record(): void
    {
        $user = User::factory()->create();

        // First score
        $this->actingAs($user)->postJson('/games/memory/score', [
            'level' => 1,
            'time_seconds' => 45.5,
            'moves' => 20,
        ]);

        // Better score (less time)
        $response = $this->actingAs($user)->postJson('/games/memory/score', [
            'level' => 1,
            'time_seconds' => 30.0,
            'moves' => 18,
        ]);

        $response->assertJson(['updated' => true]);
        $this->assertEquals(30.0, GameScore::where('user_id', $user->id)->where('level', 1)->value('time_seconds'));
    }

    public function test_worse_score_does_not_update(): void
    {
        $user = User::factory()->create();

        // First score
        $this->actingAs($user)->postJson('/games/memory/score', [
            'level' => 1,
            'time_seconds' => 30.0,
            'moves' => 18,
        ]);

        // Worse score
        $response = $this->actingAs($user)->postJson('/games/memory/score', [
            'level' => 1,
            'time_seconds' => 45.5,
            'moves' => 25,
        ]);

        $response->assertJson(['updated' => false]);
        $this->assertEquals(30.0, GameScore::where('user_id', $user->id)->where('level', 1)->value('time_seconds'));
    }

    public function test_invalid_level_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/games/memory/score', [
                'level' => 99,
                'time_seconds' => 30.0,
                'moves' => 18,
            ]);

        $response->assertUnprocessable();
    }
}
