<?php

use App\Http\Controllers\FishUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BugUserController;
use App\Http\Controllers\FossilUserController;
use App\Http\Controllers\ArtUserController;
use App\Http\Controllers\SeaCreatureUserController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/critterpedia/fish', function () {
    return Inertia::render('FishListView');
})->name('fish.list');
Route::get('/catalogo', function () {
    return Inertia::render('Catalogo');
})->name('catalogo'); // <-- Aquí es donde va

Route::get('/bichos', function () {
    return Inertia::render('BugPedia'); // <-- apunta al archivo Pages/BichosPage.vue
})->name('bichos');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});
require __DIR__ . '/auth.php';
