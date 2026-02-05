<?php

namespace App\Http\Controllers;

use App\Models\Hourly_Music;
use App\Models\Villager;
use App\Models\Fish;
use App\Models\Bug;
use App\Models\Sea_Creature;
use App\Services\MuseumStatsService;
use App\Services\MusicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __construct(
        private readonly MuseumStatsService $museumStatsService,
        private readonly MusicService $musicService,
    ) {}

    public function __invoke(Request $request)
    {
        $user = auth()->user();

        $today = now()->format('j/n');
        $birthdayVillagers = Cache::remember("birthday_villagers_{$today}", 86400, fn() =>
            Villager::where('birthday', $today)->get()
        );

        $hourlyMusic = Cache::remember('hourly_music_all', 86400, fn() => Hourly_Music::all())
            ->groupBy('hour')
            ->map(fn($songs) => $songs->map(fn($song) => [
                'id' => $song->id,
                'titulo' => $this->musicService->formatTitle($song->file_name, $song->weather),
                'autor' => 'Totakeke',
                'weather' => $song->weather,
                'src' => asset($song->music_uri),
            ])->values());

        return Inertia::render('Welcome', [
            'canLogin'          => Route::has('login'),
            'canRegister'       => Route::has('register'),
            'randomVillagers'   => Villager::inRandomOrder()->limit(15)->get(),
            'birthdayVillagers' => $birthdayVillagers,
            'hourlyMusic'       => $hourlyMusic,
            'fish'              => Cache::remember('welcome_fish', 600, fn() =>
                Fish::select(['id', 'name_es', 'name_en', 'icon', 'price', 'location', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),
            'bugs'              => Cache::remember('welcome_bugs', 600, fn() =>
                Bug::select(['id', 'name_es', 'name_en', 'icon', 'price', 'location', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),
            'seaCreatures'      => Cache::remember('welcome_sea_creatures', 600, fn() =>
                Sea_Creature::select(['id', 'name_es', 'name_en', 'icon', 'price', 'month_array_northern', 'time_array', 'is_all_day'])->get()
            ),
            'stats'             => $user ? $this->museumStatsService->getStatsForUser($user) : null,
            'maximos'           => $this->museumStatsService->getMaxCounts(),
        ]);
    }

    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'stats'   => $this->museumStatsService->getStatsForUser($user),
            'maximos' => $this->museumStatsService->getMaxCounts(),
        ]);
    }
}
