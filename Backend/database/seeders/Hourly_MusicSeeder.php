<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hourly_Music;

class Hourly_MusicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON desde storage/app/data/hourly.json
        $json = file_get_contents(storage_path('app/data/hourly.json'));
        $tracks = json_decode($json, true);

        // 2. Validar lectura
        if (!$tracks) {
            throw new \Exception("No se pudo leer hourly.json o está mal formado");
        }

        // 3. Recorrer cada track y crear registro
        foreach ($tracks as $key => $t) {
            Hourly_Music::create([
                'file_name' => $t['file-name'],
                'hour' => $t['hour'] ?? 0,
                'weather' => $t['weather'] ?? null,
                'music_uri' => $t['music_uri'] ?? null,
            ]);
        }
    }
}
