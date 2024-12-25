<?php

namespace Database\Seeders;

use App\Models\Hafiz;
use Illuminate\Database\Seeder;

class HafizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hafiz::factory(10)->create();
    }
}
