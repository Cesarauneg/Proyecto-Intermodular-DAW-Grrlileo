<?php

namespace App\Http\Controllers;

use App\Models\Hourly_Music;
use App\Models\Villager;
use App\Models\Fish;
use App\Models\Bug;
use App\Models\Sea_Creature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     * Prepara y renderiza la página de bienvenida con todos los datos necesarios.
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        
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


         // Stats del museo

        $stats = $user ? [
            'peces'     => $user->fish()->wherePivot('donated_to_museum', true)->count(),
            'bichos'    => $user->bugs()->wherePivot('donated_to_museum', true)->count(),
            'arte'      => $user->art()->wherePivot('donated_to_museum', true)->count(),
            'fosiles'   => $user->fossils()->wherePivot('donated_to_museum', true)->count(),
            'criaturas' => $user->seaCreatures()->wherePivot('donated_to_museum', true)->count(),
        ] : null;

        $maximos = [
            'peces'     => 80,
            'bichos'    => 80,
            'arte'      => 43,
            'fosiles'   => 73,
            'criaturas' => 40,
        ];

         // Render
        return Inertia::render('Welcome', [
            'canLogin'          => Route::has('login'),
            'canRegister'       => Route::has('register'),
            'randomVillagers'   => Villager::inRandomOrder()->limit(15)->get(),
            'birthdayVillagers' => $birthdayVillagers,
            'hourlyMusic'       => $hourlyMusic,

            // Colecciones con caché (10 minutos)
            'fish'         => Cache::remember('welcome_fish', 600, fn() =>
                Fish::select(['id', 'name_es', 'name_en', 'icon', 'price', 'location', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),
            'bugs'         => Cache::remember('welcome_bugs', 600, fn() =>
                Bug::select(['id', 'name_es', 'name_en', 'icon', 'price', 'location', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),
            'seaCreatures' => Cache::remember('welcome_sea_creatures', 600, fn() =>
                Sea_Creature::select(['id', 'name_es', 'name_en', 'icon', 'price', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),

            // Estadisticas del Museo
            'stats'   => $stats,
            'maximos'=> $maximos,
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

     public function index()
    {
        $user = auth()->user();

        $stats = [
            'peces'     => $user->fish()->wherePivot('donated_to_museum', true)->count(),
            'bichos'    => $user->bugs()->wherePivot('donated_to_museum', true)->count(),
            'arte'      => $user->art()->wherePivot('donated_to_museum', true)->count(),
            'fosiles'   => $user->fossils()->wherePivot('donated_to_museum', true)->count(),
            'criaturas' => $user->seaCreatures()->wherePivot('donated_to_museum', true)->count(),
        ];

        $totalesMaximos = [
            'peces'     => 80, 
            'bichos'    => 80, 
            'arte'      => 43, 
            'fosiles'   => 73, 
            'criaturas' => 40
        ];

return Inertia::render('Dashboard', [
    'stats' => $stats,  
    'maximos' => $totalesMaximos
]);
    }
}
