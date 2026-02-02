<?php

namespace App\Http\Controllers;

use App\Models\Hourly_Music;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para gestionar la música por hora.
 *
 * La tabla `hourly_music` contiene 72 registros:
 * - 24 horas (0-23) × 3 variantes de clima (Sunny, Rainy, Snowy)
 * - Cada registro tiene: file_name, hour, weather, music_uri
 *
 * Los archivos de audio están en: /public/music/hourly/BGM_24Hour_[HH]_[Weather].mp3
 */
class HourlyMusicController extends Controller
{
    /**
     * Obtiene todas las canciones de la base de datos.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Hourly_Music::all());
    }

    /**
     * Obtiene las 3 variantes de música para una hora específica.
     *
     * @param int $hour Hora del día (0-23)
     * @return JsonResponse Array con las 3 canciones (Sunny, Rainy, Snowy)
     */
    public function getByHour(int $hour): JsonResponse
    {
        // Validar que la hora esté en rango válido
        $hour = max(0, min(23, $hour));

        $songs = Hourly_Music::where('hour', $hour)
            ->orderBy('weather')
            ->get()
            ->map(fn($song) => [
                'id' => $song->id,
                'titulo' => $this->formatTitle($song->file_name, $song->weather),
                'autor' => 'Totakeke',
                'weather' => $song->weather,
                'src' => asset($song->music_uri),
            ]);

        return response()->json($songs);
    }

    /**
     * Obtiene la hora actual del servidor (para sincronización inicial).
     * El cliente detectará su hora local, pero este endpoint sirve como fallback.
     *
     * @return JsonResponse
     */
    public function getCurrentHour(): JsonResponse
    {
        return response()->json([
            'hour' => (int) now()->format('G'),
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Formatea el título de la canción para mostrar al usuario.
     * Convierte "BGM_24Hour_00_Rainy" en "00:00 - Lluvioso"
     *
     * @param string $fileName Nombre del archivo
     * @param string|null $weather Clima (Sunny, Rainy, Snowy)
     * @return string Título formateado
     */
    private function formatTitle(string $fileName, ?string $weather): string
    {
        // Extraer la hora del nombre del archivo (BGM_24Hour_XX_Weather)
        preg_match('/BGM_24Hour_(\d{2})/', $fileName, $matches);
        $hour = $matches[1] ?? '00';

        // Traducir el clima al español
        $weatherTranslations = [
            'Sunny' => 'Soleado',
            'Rainy' => 'Lluvioso',
            'Snowy' => 'Nevado',
        ];

        $weatherEs = $weatherTranslations[$weather] ?? $weather;

        return "{$hour}:00 - {$weatherEs}";
    }
}
