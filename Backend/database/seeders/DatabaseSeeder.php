<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       /* User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);*/
        $this->call([
            VillagerSeeder::class,
            FishSeeder::class,
            BugSeeder::class,
            FossilSeeder::class,
            ArtSeeder::class,
            Sea_CreatureSeeder::class,
            Hourly_MusicSeeder::class,
            Totakeke_MusicSeeder::class,
        ]);

    }
}
