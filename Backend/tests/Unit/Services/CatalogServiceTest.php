<?php

namespace Tests\Unit\Services;

use App\Models\Bug;
use App\Services\CatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CatalogServiceTest extends TestCase
{
    use RefreshDatabase;

    private CatalogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CatalogService();
    }

    public function test_get_all_returns_collection(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);

        $result = $this->service->getAll(Bug::class);

        $this->assertCount(1, $result);
        $this->assertEquals('Abeja', $result->first()->name_es);
    }

    public function test_apply_filters_by_column(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);
        Bug::create(['file_name' => 'mariposa', 'name_es' => 'Mariposa', 'name_en' => 'Butterfly', 'price' => 2500, 'location' => 'Volando', 'rarity' => 'Raro']);

        $request = Request::create('/test', 'GET', ['rarity' => 'Raro']);
        $query = $this->service->applyFilters(Bug::class, $request, ['rarity', 'location']);

        $result = $query->get();
        $this->assertCount(1, $result);
        $this->assertEquals('Mariposa', $result->first()->name_es);
    }

    public function test_apply_filters_with_search(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);
        Bug::create(['file_name' => 'mariposa', 'name_es' => 'Mariposa', 'name_en' => 'Butterfly', 'price' => 2500, 'location' => 'Volando', 'rarity' => 'Raro']);

        $request = Request::create('/test', 'GET', ['search' => 'Abeja']);
        $query = $this->service->applyFilters(Bug::class, $request, ['rarity']);

        $result = $query->get();
        $this->assertCount(1, $result);
    }

    public function test_apply_filters_with_price_order(): void
    {
        Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);
        Bug::create(['file_name' => 'mariposa', 'name_es' => 'Mariposa', 'name_en' => 'Butterfly', 'price' => 2500, 'location' => 'Volando', 'rarity' => 'Raro']);

        $request = Request::create('/test', 'GET', ['price_order' => 'desc']);
        $query = $this->service->applyFilters(Bug::class, $request, []);

        $result = $query->get();
        $this->assertGreaterThanOrEqual($result[1]->price, $result[0]->price);
    }
}
