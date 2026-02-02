<?php

use App\Http\Controllers\FishUserController;
use App\Http\Controllers\ProfileController;
use App\Models\Hourly_Music;
use App\Models\Villager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BugUserController;
use App\Http\Controllers\FossilUserController;
use App\Http\Controllers\ArtUserController;
use App\Http\Controllers\SeaCreatureUserController;

Route::get('/', function () {
    // Obtener aldeanos que cumplen años hoy (formato: día/mes)
    $today = now()->format('j/n'); // j = día sin cero, n = mes sin cero
    $birthdayVillagers = Villager::where('birthday', $today)->get();

    // Obtener música agrupada por hora
    // según su hora local
    $hourlyMusic = Hourly_Music::all()
        ->groupBy('hour')
        ->map(fn($songs) => $songs->map(fn($song) => [
            'id' => $song->id,
            'titulo' => formatMusicTitle($song->file_name, $song->weather),
            'autor' => 'Totakeke',
            'weather' => $song->weather,
            'src' => asset($song->music_uri),
        ])->values());

    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
        'randomVillagers' => Villager::inRandomOrder()->limit(15)->get(),
        'birthdayVillagers' => $birthdayVillagers,
        'hourlyMusic' => $hourlyMusic,
    ]);
});

/**
 * Formatea el título de la música horaria.
 * Convierte "BGM_24Hour_00_Rainy" en "00:00 - Lluvioso"
 */
function formatMusicTitle(string $fileName, ?string $weather): string
{
    preg_match('/BGM_24Hour_(\d{2})/', $fileName, $matches);
    $hour = $matches[1] ?? '00';

    $weatherTranslations = [
        'Sunny' => 'Soleado',
        'Rainy' => 'Lluvioso',
        'Snowy' => 'Nevado',
    ];

    $weatherEs = $weatherTranslations[$weather] ?? $weather;
    return "{$hour}:00 - {$weatherEs}";
}

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
