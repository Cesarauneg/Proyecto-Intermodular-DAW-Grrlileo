<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeaCreatureResource;
use App\Models\Sea_Creature;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class Sea_CreatureController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function index()
    {
        return SeaCreatureResource::collection($this->catalogService->getAll(Sea_Creature::class));
    }

    public function filter(Request $request)
    {
        $query = $this->catalogService->applyFilters(Sea_Creature::class, $request, ['speed', 'shadow']);

        return SeaCreatureResource::collection($query->get());
    }

    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return SeaCreatureResource::collection($this->catalogService->getAvailable(Sea_Creature::class, $hemisphere));
    }
}
