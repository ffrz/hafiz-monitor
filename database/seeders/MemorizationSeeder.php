<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = DB::connection('online_db')->table('memorizations')->get();
        foreach ($rows as $row) {
            DB::connection()->table('memorizations')->insert((array)$row);
        }
    }
}
