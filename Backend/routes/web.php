<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoryGameController;
use App\Http\Controllers\VillagerUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/art', fn() => Inertia::render('ArtPedia'))->name('art.list');
Route::get('/fish', fn() => Inertia::render('FishPedia'))->name('fish.list');
Route::get('/sea_creatures', fn() => Inertia::render('SeaCreaturePedia'))->name('sea_creature.list');
Route::get('/fossils', fn() => Inertia::render('FossilPedia'))->name('fossil.list');
Route::get('/fossils2', fn() => Inertia::render('FossilGallery'))->name('fossil2.list');
Route::get('/catalogo', fn() => Inertia::render('Catalogo'))->name('catalogo');
Route::get('/bugs', fn() => Inertia::render('BugPedia'))->name('bichos');

Route::get('/dashboard', fn() => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/collection', fn() => Inertia::render('Collection'))->name('collection');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/games/memory/{level}', [MemoryGameController::class, 'show'])->name('memory-game.show');
    Route::get('/games/memory/{level}/data', [MemoryGameController::class, 'data'])->name('memory-game.data');
    Route::post('/games/memory/score', [MemoryGameController::class, 'store'])->name('memory-game.store');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{type}/{itemId}/donate', [DonationController::class, 'donate'])
        ->where('type', 'fish|bugs|fossils|art|sea_creatures');
    Route::get('/user/{type}', [DonationController::class, 'index'])
        ->where('type', 'fish|bugs|fossils|art|sea_creatures');

    Route::post('/villagers/{villager}/favorite', [VillagerUserController::class, 'favorite']);
    Route::get('/user/villagers', [VillagerUserController::class, 'index']);
});

require __DIR__ . '/auth.php';
