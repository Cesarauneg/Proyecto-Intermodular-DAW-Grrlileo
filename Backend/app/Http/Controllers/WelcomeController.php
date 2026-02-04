<?php

namespace App\Http\Controllers;

use App\Models\Hourly_Music;
use App\Models\Villager;
use App\Models\Fish;
use App\Models\Bug;
use App\Models\Sea_Creature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     * Prepara y renderiza la página de bienvenida con todos los datos necesarios.
     */
    public function __invoke(Request $request)
    {
        // Obtener aldeanos que cumplen años hoy (formato: día/mes)
        $today = now()->format('j/n'); // j = día sin cero, n = mes sin cero
        $birthdayVillagers = Villager::where('birthday', $today)->get();

        // Obtener música agrupada por hora
        $hourlyMusic = Hourly_Music::all()
            ->groupBy('hour')
            ->map(fn($songs) => $songs->map(fn($song) => [
                'id' => $song->id,
                'titulo' => $this->formatMusicTitle($song->file_name, $song->weather),
                'autor' => 'Totakeke',
                'weather' => $song->weather,
                'src' => asset($song->music_uri),
            ])->values());

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'randomVillagers' => Villager::inRandomOrder()->limit(15)->get(),
            'birthdayVillagers' => $birthdayVillagers,
            'hourlyMusic' => $hourlyMusic,
            'fish' => Fish::all(),
            'bugs' => Bug::all(),
            'seaCreatures' => Sea_Creature::all(),
        ]);
    }

    /**
     * Formatea el título de la música horaria.
     * Convierte "BGM_24Hour_00_Rainy" en "00:00 - Lluvioso"
     */
    private function formatMusicTitle(string $fileName, ?string $weather): string
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
}
