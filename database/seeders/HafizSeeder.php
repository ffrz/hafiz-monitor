<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HafizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = DB::connection('online_db')->table('hafizes')->get();
        foreach ($rows as $row) {
            DB::connection()->table('hafizes')->insert((array)$row);
        }
    }
}
