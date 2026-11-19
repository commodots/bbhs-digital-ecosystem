<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Production baseline only. Demo accounts and transactional records
        // must be seeded separately and only when explicitly required.
        $this->call([
            BbhsOrganisationSeeder::class,
            BbhsNationalExecutivesSeeder::class,
        ]);
    }
}
