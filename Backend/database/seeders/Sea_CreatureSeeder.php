<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sea_Creature;

class Sea_CreatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Leer el JSON desde database/seeders/data/sea.json
        $json = file_get_contents(database_path('seeders/data/sea.json'));
        $creatures = json_decode($json, true);

        // 2️⃣ Validar que se haya leído correctamente
        if (!$creatures) {
            throw new \Exception("No se pudo leer sea.json o está mal formado");
        }

        // 3️⃣ Recorrer cada criatura
        foreach ($creatures as $key => $c) {
            Sea_Creature::create([
                // Datos básicos
                'file_name' => $c['file-name'],
                'speed' => $c['speed'] ?? null,
                'shadow' => $c['shadow'] ?? null,

                // Disponibilidad
                'month_array_northern' => json_encode($c['availability']['month-array-northern'] ?? null),
                'month_array_southern' => json_encode($c['availability']['month-array-southern'] ?? null),
                'time_array' => json_encode($c['availability']['time-array'] ?? null),
                'is_all_day' => $c['availability']['isAllDay'] ?? false,
                'is_all_year' => $c['availability']['isAllYear'] ?? false,

                // Precio
                'price' => $c['price'] ?? null,

                // Nombres y textos
                'name_en' => $c['name']['name-USen'] ?? null,
                'name_es' => $c['name']['name-USes'] ?? null,
                'catch_phrase_en' => $c['catch-phrase'] ?? null,
                'museum_phrase_en' => $c['museum-phrase'] ?? null,

                // Imágenes locales
                'image' => 'images/sea_creatures/' . $c['file-name'] . '.png',
                'icon' => 'icons/sea_creatures/' . $c['file-name'] . '.png',
            ]);
        }
    }
}
