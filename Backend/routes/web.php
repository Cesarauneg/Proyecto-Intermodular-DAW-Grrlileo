<?php

use App\Http\Controllers\BugUserController;
use App\Http\Controllers\FossilUserController;
use App\Http\Controllers\ArtUserController;
use App\Http\Controllers\SeaCreatureUserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FishUserController;
use App\Http\Controllers\ProfileController;
use App\Models\Hourly_Music;
use App\Http\Controllers\VillagerUserController;
use App\Http\Controllers\DashboardController;
use App\Models\Villager;
use App\Models\Fish;
use App\Models\Bug;
use App\Models\Sea_Creature;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/art', function () {
    return Inertia::render('ArtPedia');
})->name('art.list');

Route::get('/fish', function () {
    return Inertia::render('FishPedia');
})->name('fish.list');

Route::get('/sea_creatures', function () {
    return Inertia::render('SeaCreaturePedia');
})->name('sea_creature.list');

Route::get('/fossils', function () {
    return Inertia::render('FossilPedia');
})->name('fossil.list');

Route::get('/fossils2', function () {
    return Inertia::render('FossilGallery');
})->name('fossil2.list');

Route::get('/catalogo', function () {
    return Inertia::render('Catalogo');
})->name('catalogo');

Route::get('/bugs', function () {
    return Inertia::render('BugPedia');
})->name('bichos');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/collection', function() {
    return Inertia::render('Collection');
})->name('collection');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/fish/{fish}/donate', [FishUserController::class, 'donate']);
    Route::get('/user/fish', [FishUserController::class, 'index']);
    Route::post('/bugs/{bug}/donate', [BugUserController::class, 'donate']);
    Route::get('/user/bugs', [BugUserController::class, 'index']);
    Route::post('/fossils/{fossil}/donate', [FossilUserController::class, 'donate']);
    Route::get('/user/fossils', [FossilUserController::class, 'index']);
    Route::post('/art/{art}/donate', [ArtUserController::class, 'donate']);
    Route::get('/user/art', [ArtUserController::class, 'index']);
    Route::post('/sea_creatures/{sea_creature}/donate', [SeaCreatureUserController::class, 'donate']);
    Route::get('/user/sea_creatures', [SeaCreatureUserController::class, 'index']);
    Route::post('/villagers/{villager}/favorite', [VillagerUserController::class, 'favorite']);
    Route::get('/user/villagers', [VillagerUserController::class, 'index']);
});
require __DIR__ . '/auth.php';
