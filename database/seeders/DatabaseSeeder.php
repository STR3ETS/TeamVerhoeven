<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Andere seeders kun je hier ook toevoegen
        $this->call([
            CoachesSeeder::class,
        ]);
    }
}
