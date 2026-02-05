<?php

namespace Tests\Feature;

use App\Models\Bug;
use App\Models\Fish;
use App\Models\Art;
use App\Models\Fossil;
use App\Models\Sea_Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_bugs_index_returns_json(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);

        $response = $this->getJson('/api/bugs');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_fish_index_returns_json(): void
    {
        Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $response = $this->getJson('/api/fish');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_art_index_returns_json(): void
    {
        Art::create(['file_name' => 'pintura_famosa', 'name_es' => 'Pintura famosa', 'name_en' => 'Famous Painting', 'buy_price' => 4980, 'has_fake' => true]);

        $response = $this->getJson('/api/art');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_fossil_index_returns_json(): void
    {
        Fossil::create(['file_name' => 'craneo_trex', 'name_es' => 'Cráneo T-Rex', 'name_en' => 'T. Rex Skull', 'price' => 6000, 'part_of' => 'T. Rex']);

        $response = $this->getJson('/api/fossils');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_sea_creatures_index_returns_json(): void
    {
        Sea_Creature::create(['file_name' => 'anemona', 'name_es' => 'Anémona', 'name_en' => 'Sea Anemone', 'price' => 500, 'speed' => 'Inmóvil', 'shadow' => 'Pequeña']);

        $response = $this->getJson('/api/sea_creatures');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_bugs_filter_by_rarity(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);
        Bug::create(['file_name' => 'mariposa', 'name_es' => 'Mariposa', 'name_en' => 'Butterfly', 'price' => 2500, 'location' => 'Volando', 'rarity' => 'Raro']);

        $response = $this->getJson('/api/bugs/filter?rarity=Raro');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_fish_filter_by_price_order(): void
    {
        Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);
        Fish::create(['file_name' => 'tiburon', 'name_es' => 'Tiburón', 'name_en' => 'Shark', 'price' => 15000, 'location' => 'Mar', 'rarity' => 'Raro']);

        $response = $this->getJson('/api/fish/filter?price_order=desc');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual($data[1]['price'], $data[0]['price']);
    }
}
