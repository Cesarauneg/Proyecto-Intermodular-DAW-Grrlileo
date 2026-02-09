<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Art;
use Illuminate\Support\Facades\Storage;

class ArtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON desde database/seeders/data/art.json
        $json = file_get_contents(database_path('seeders/data/art.json'));
        $arts = json_decode($json, true);

        // 2. Validar que se haya leído correctamente
        if (!$arts) {
            throw new \Exception("No se pudo leer art.json o está mal formado");
        }

        // 3. Recorrer cada obra de arte y crear el registro en DB
        foreach ($arts as $key => $a) {
            Art::create([
                'file_name' => $a['file-name'],
                'has_fake' => $a['hasFake'] ?? false,
                'buy_price' => $a['buy-price'] ?? null,
                'sell_price' => $a['sell-price'] ?? null,

                // Nombres en inglés y español
                'name_en' => $a['name']['name-USen'] ?? null,
                'name_es' => $a['name']['name-USes'] ?? null,

                // Descripción del museo
                'museum_desc_en' => $a['museum-desc'] ?? null,

                // Imagen local
                'image' => 'images/art/' . $a['file-name'] . '.png',
            ]);
        }
    }
}
