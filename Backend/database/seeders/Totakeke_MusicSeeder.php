<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Totakeke_Music;

class Totakeke_MusicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON desde storage/app/data/music.json
        $json = file_get_contents(storage_path('app/data/music.json'));
        $tracks = json_decode($json, true);

        if (!$tracks) {
            throw new \Exception("No se pudo leer music.json o está mal formado");
        }

        // 2. Recorrer cada canción
        foreach ($tracks as $key => $t) {
            Totakeke_Music::create([
                // Datos básicos
                'file_name' => $t['file-name'],

                // Nombres en inglés y español
                'name_en' => $t['name']['name-USen'] ?? null,
                'name_es' => $t['name']['name-USes'] ?? null,

                // Precios y disponibilidad
                'buy_price' => $t['buy-price'] ?? rand(4000, 5000), // Valor por defecto si es null
                'sell_price' => $t['sell-price'] ?? null,
                'is_orderable' => $t['isOrderable'] ?? false,

                // Rutas de música e imagen
                'music_uri' => $t['music_uri'] ?? null,
                'image' => $t['image_uri'] ?? null,
            ]);
        }
    }
}
