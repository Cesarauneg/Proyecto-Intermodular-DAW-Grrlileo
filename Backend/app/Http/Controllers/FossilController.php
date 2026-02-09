<?php

namespace App\Http\Controllers;

use App\Http\Resources\FossilResource;
use App\Models\Fossil;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class FossilController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function index()
    {
        return FossilResource::collection($this->catalogService->getAll(Fossil::class));
    }

    public function filter(Request $request)
    {
        $query = $this->catalogService->applyFilters(Fossil::class, $request, ['part_of']);

        return FossilResource::collection($query->get());
    }
}
