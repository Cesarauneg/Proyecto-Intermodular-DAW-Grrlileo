<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fossil;

class FossilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Leer el JSON desde storage/app/data/fossils.json
        $json = file_get_contents(storage_path('app/data/fossils.json'));
        $fossils = json_decode($json, true);

        // 2. Validar que se haya leído correctamente
        if (!$fossils) {
            throw new \Exception("No se pudo leer fossils.json o está mal formado");
        }

        // 3. Recorrer cada fósil y crear el registro en DB
        foreach ($fossils as $key => $f) {
            Fossil::create([
                'file_name' => $f['file-name'],
                'part_of' => $f['part-of'] ?? null,
                'price' => $f['price'] ?? null,
                'name_en' => $f['name']['name-USen'] ?? null,
                'name_es' => $f['name']['name-USes'] ?? null, 
                'museum_phrase_en' => $f['museum-phrase'] ?? null,
                'image' => 'images/fossils/' . $f['file-name'] . '.png',
            ]);
        }
    }
}
