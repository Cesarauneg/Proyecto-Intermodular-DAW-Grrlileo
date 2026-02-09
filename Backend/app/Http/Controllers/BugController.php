<?php

namespace App\Http\Controllers;

use App\Http\Resources\BugResource;
use App\Models\Bug;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class BugController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function index()
    {
        return BugResource::collection($this->catalogService->getAll(Bug::class));
    }

    public function filter(Request $request)
    {
        $query = $this->catalogService->applyFilters(Bug::class, $request, ['rarity', 'location']);

        return BugResource::collection($query->get());
    }

    public function available(Request $request)
    {
        $hemisphere = $request->get('hemisphere', 'north');

        return BugResource::collection($this->catalogService->getAvailable(Bug::class, $hemisphere));
    }
}
