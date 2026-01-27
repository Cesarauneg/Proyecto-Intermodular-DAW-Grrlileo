<?php
namespace Database\Seeders;

use App\Models\Villager;
use Illuminate\Database\Seeder;

class VillagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cargar el JSON desde storage/data/villagers.json
        $json      = file_get_contents(storage_path('app/data/villagers.json'));
        $villagers = json_decode($json, true);

        if (! $villagers) {
            throw new \Exception("No se pudo leer el JSON o está mal formado");
        }

        // 2. Recorrer cada villager del JSON
        foreach ($villagers as $key => $v) {

            // 3. Crear el villager en la base de datos
            Villager::create([
                // Datos básicos
                'file_name'       => $v['file-name'],
                'personality'     => $v['personality'],
                'birthday_string' => $v['birthday-string'],
                'birthday'        => $v['birthday'],
                'species'         => $v['species'],
                'gender'          => $v['gender'],
                'subtype'         => $v['subtype'] ?? null,
                'hobby'           => $v['hobby'] ?? null,
                'bubble_color'    => $v['bubble-color'] ?? null,
                'text_color'      => $v['text-color'] ?? null,
                'saying'          => $v['saying'] ?? null,

                // Nombres
                'name_en'         => $v['name']['name-USen'],
                'name_es'         => $v['name']['name-USes'] ?? null,

                // Catch phrases
                'catch_en'        => $v['catch-translations']['catch-USen'] ?? null,
                'catch_es'        => $v['catch-translations']['catch-USes'] ?? null,

                // Imágenes locales
                'image'           => 'images/villagers/' . $v['file-name'] . '.png',
                'icon'            => 'icons/villagers/' . $v['file-name'] . '.png',
            ]);
        }
    }
}
