<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fish;
use Illuminate\Support\Facades\Storage;

class FishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON de fish
        $json = file_get_contents(database_path('seeders/data/fish.json')); 
        $fishes = json_decode($json, true);

        // Validar que se haya leído correctamente
        if (!$fishes) {
            throw new \Exception("No se pudo leer fish.json o está mal formado");
        }

        // 2. Recorrer cada fish
        foreach ($fishes as $key => $f) {
            Fish::create([
                // Datos básicos
                'file_name' => $f['file-name'],
                'location' => $f['availability']['location'] ?? null,
                'shadow' => $f['shadow'] ?? null,
                'rarity' => $f['availability']['rarity'] ?? null,

                // Disponibilidad
                'month_array_northern' => json_encode($f['availability']['month-array-northern'] ?? []),
                'month_array_southern' => json_encode($f['availability']['month-array-southern'] ?? []),
                'time_array' => json_encode($f['availability']['time-array'] ?? []),
                'is_all_day' => $f['availability']['isAllDay'] ?? false,
                'is_all_year' => $f['availability']['isAllYear'] ?? false,

                // Precio
                'price' => $f['price'] ?? null,
                'price_cj' => $f['price-cj'] ?? null,

                // Nombres y frases
                'name_en' => $f['name']['name-USen'] ?? null,
                'name_es' => $f['name']['name-USes'] ?? null,
                'catch_phrase_en' => $f['catch-phrase'] ?? null,
                'museum_phrase_en' => $f['museum-phrase'] ?? null,

                // Imágenes locales
                'image' => 'images/fish/' . $f['file-name'] . '.png',
                'icon' => 'icons/fish/' . $f['file-name'] . '.png',
            ]);
        }
    }
}
