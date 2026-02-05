<?php

namespace App\Http\Controllers;

use App\Services\CollectionService;
use App\Services\MuseumStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    private const RELATIONSHIP_MAP = [
        'fish'          => 'fish',
        'bugs'          => 'bugs',
        'fossils'       => 'fossils',
        'art'           => 'art',
        'sea_creatures' => 'seaCreatures',
    ];

    public function __construct(
        private readonly CollectionService $collectionService,
        private readonly MuseumStatsService $museumStatsService,
    ) {}

    public function donate(Request $request, string $type, int $itemId): JsonResponse
    {
        $relationship = self::RELATIONSHIP_MAP[$type];
        $user = $request->user();

        $this->collectionService->toggle($user, $itemId, $relationship);
        $this->museumStatsService->invalidateCache($user->id);

        return response()->json(['success' => true]);
    }

    public function index(Request $request, string $type): JsonResponse
    {
        $relationship = self::RELATIONSHIP_MAP[$type];

        $items = $this->collectionService->getUserItems($request->user(), $relationship);

        return response()->json($items);
    }
}
