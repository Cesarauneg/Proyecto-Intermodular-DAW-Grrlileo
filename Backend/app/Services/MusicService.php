<?php

namespace App\Services;

class MusicService
{
    private const WEATHER_TRANSLATIONS = [
        'Sunny' => 'Soleado',
        'Rainy' => 'Lluvioso',
        'Snowy' => 'Nevado',
    ];

    public function formatTitle(string $fileName, ?string $weather): string
    {
        preg_match('/BGM_24Hour_(\d{2})/', $fileName, $matches);
        $hour = $matches[1] ?? '00';

        $weatherEs = self::WEATHER_TRANSLATIONS[$weather] ?? $weather;

        return "{$hour}:00 - {$weatherEs}";
    }
}
