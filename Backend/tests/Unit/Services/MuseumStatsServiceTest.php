<?php

namespace Tests\Unit\Services;

use App\Models\Bug;
use App\Models\Fish;
use App\Models\User;
use App\Services\MuseumStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MuseumStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    private MuseumStatsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MuseumStatsService();
    }

    public function test_empty_stats_for_new_user(): void
    {
        $user = User::factory()->create();
        $stats = $this->service->getStatsForUser($user);

        $this->assertEquals(0, $stats['peces']);
        $this->assertEquals(0, $stats['bichos']);
        $this->assertEquals(0, $stats['arte']);
        $this->assertEquals(0, $stats['fosiles']);
        $this->assertEquals(0, $stats['criaturas']);
    }

    public function test_counts_donated_items_correctly(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);
        $bug = Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);

        $user->fish()->attach($fish->id, ['donated_to_museum' => true]);
        $user->bugs()->attach($bug->id, ['donated_to_museum' => true]);

        $stats = $this->service->getStatsForUser($user);

        $this->assertEquals(1, $stats['peces']);
        $this->assertEquals(1, $stats['bichos']);
    }

    public function test_does_not_count_non_donated_items(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $user->fish()->attach($fish->id, ['donated_to_museum' => false]);

        $stats = $this->service->getStatsForUser($user);

        $this->assertEquals(0, $stats['peces']);
    }

    public function test_stats_are_cached(): void
    {
        $user = User::factory()->create();

        // First call - should cache
        $this->service->getStatsForUser($user);
        $this->assertTrue(Cache::has("museum_stats_{$user->id}"));
    }

    public function test_invalidate_cache_removes_cached_stats(): void
    {
        $user = User::factory()->create();

        $this->service->getStatsForUser($user);
        $this->service->invalidateCache($user->id);

        $this->assertFalse(Cache::has("museum_stats_{$user->id}"));
    }

    public function test_max_counts_constant(): void
    {
        $this->assertEquals([
            'peces' => 80,
            'bichos' => 80,
            'arte' => 43,
            'fosiles' => 73,
            'criaturas' => 40,
        ], MuseumStatsService::MAX_COUNTS);
    }
}
