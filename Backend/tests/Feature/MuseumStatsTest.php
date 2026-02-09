<?php

namespace Tests\Feature;

use App\Models\Bug;
use App\Models\Fish;
use App\Models\User;
use App\Services\MuseumStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MuseumStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_returns_correct_counts(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);
        $user->fish()->attach($fish->id, ['donated_to_museum' => true]);

        $service = app(MuseumStatsService::class);
        $stats = $service->getStatsForUser($user);

        $this->assertEquals(1, $stats['peces']);
        $this->assertEquals(0, $stats['bichos']);
    }

    public function test_max_counts_are_correct(): void
    {
        $service = app(MuseumStatsService::class);
        $maxCounts = $service->getMaxCounts();

        $this->assertEquals(80, $maxCounts['peces']);
        $this->assertEquals(80, $maxCounts['bichos']);
        $this->assertEquals(43, $maxCounts['arte']);
        $this->assertEquals(73, $maxCounts['fosiles']);
        $this->assertEquals(40, $maxCounts['criaturas']);
    }

    public function test_cache_invalidation_works(): void
    {
        $user = User::factory()->create();

        $service = app(MuseumStatsService::class);

        // Prime the cache
        $service->getStatsForUser($user);
        $this->assertTrue(Cache::has("museum_stats_{$user->id}"));

        // Invalidate
        $service->invalidateCache($user->id);
        $this->assertFalse(Cache::has("museum_stats_{$user->id}"));
    }
}
