<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtResource;
use App\Models\Art;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class ArtController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function index()
    {
        return ArtResource::collection($this->catalogService->getAll(Art::class));
    }

    public function filter(Request $request)
    {
        $query = $this->catalogService->applyFilters(Art::class, $request, ['has_fake']);

        return ArtResource::collection($query->get());
    }
}
