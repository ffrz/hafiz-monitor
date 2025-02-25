<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->call([
                SurahSeeder::class,
                AyahSeeder::class,
                UserSeeder::class,
                HafizSeeder::class,
                MemorizationSeeder::class,
                MemorizationDetailSeeder::class,
            ]);
        });
    }
}
