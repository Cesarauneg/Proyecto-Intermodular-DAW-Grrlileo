<?php

namespace App\Http\Controllers;

use App\Http\Resources\FishResource;
use App\Models\Fish;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class FishController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function index()
    {
        return FishResource::collection($this->catalogService->getAll(Fish::class));
    }

    public function filter(Request $request)
    {
        $query = $this->catalogService->applyFilters(Fish::class, $request, ['rarity', 'location']);

        return FishResource::collection($query->get());
    }

    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return FishResource::collection($this->catalogService->getAvailable(Fish::class, $hemisphere));
    }
}
