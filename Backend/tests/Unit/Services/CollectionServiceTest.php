<?php

namespace Tests\Unit\Services;

use App\Models\Fish;
use App\Models\User;
use App\Services\CollectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CollectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CollectionService();
    }

    public function test_toggle_attaches_item(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $this->service->toggle($user, $fish->id, 'fish');

        $this->assertTrue(
            $user->fish()->wherePivot('donated_to_museum', true)->where('fish.id', $fish->id)->exists()
        );
    }

    public function test_toggle_detaches_item_on_second_call(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $this->service->toggle($user, $fish->id, 'fish');
        $this->service->toggle($user, $fish->id, 'fish');

        $this->assertFalse($user->fish()->where('fish.id', $fish->id)->exists());
    }

    public function test_get_user_items_filters_by_donated(): void
    {
        $user = User::factory()->create();
        $fish1 = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);
        $fish2 = Fish::create(['file_name' => 'tiburon', 'name_es' => 'Tiburón', 'name_en' => 'Shark', 'price' => 15000, 'location' => 'Mar', 'rarity' => 'Raro']);

        // Only donate fish1
        $user->fish()->attach($fish1->id, ['donated_to_museum' => true]);
        // Attach fish2 without donating
        $user->fish()->attach($fish2->id, ['donated_to_museum' => false]);

        $items = $this->service->getUserItems($user, 'fish');

        $this->assertCount(1, $items);
        $this->assertEquals($fish1->id, $items->first()->id);
    }
}
