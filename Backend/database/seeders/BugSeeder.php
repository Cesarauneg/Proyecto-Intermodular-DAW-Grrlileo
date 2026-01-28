<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bug;
use Illuminate\Support\Facades\Storage;

class BugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON desde database/seeders/data/bugs.json
        $json = file_get_contents(database_path('seeders/data/bugs.json'));
        $bugs = json_decode($json, true);

        // 2. Validar que se haya leído correctamente
        if (!$bugs) {
            throw new \Exception("No se pudo leer bugs.json o está mal formado");
        }

        // 3. Recorrer cada bug
        foreach ($bugs as $key => $b) {
            Bug::create([
                // Datos básicos
                'file_name' => $b['file-name'],
                'location' => $b['availability']['location'] ?? null,
                'rarity' => $b['availability']['rarity'] ?? null,

                // Disponibilidad
                'month_array_northern' => json_encode($b['availability']['month-array-northern'] ?? []),
                'month_array_southern' => json_encode($b['availability']['month-array-southern'] ?? []),
                'time_array' => json_encode($b['availability']['time-array'] ?? []),
                'is_all_day' => $b['availability']['isAllDay'] ?? false,
                'is_all_year' => $b['availability']['isAllYear'] ?? false,

                // Precio
                'price' => $b['price'] ?? null,
                'price_flick' => $b['price-flick'] ?? null,

                // Nombres y frases
                'name_en' => $b['name']['name-USen'] ?? null,
                'name_es' => $b['name']['name-USes'] ?? null,
                'catch_phrase_en' => $b['catch-phrase'] ?? null,
                'museum_phrase_en' => $b['museum-phrase'] ?? null,

                // Imágenes locales
                'image' => 'images/bugs/' . $b['file-name'] . '.png',
                'icon' => 'icons/bugs/' . $b['file-name'] . '.png',
            ]);
        }
    }
}
