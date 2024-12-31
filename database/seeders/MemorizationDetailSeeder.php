<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemorizationDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = DB::connection('online_db')->table('memorization_details')->get();
        foreach ($rows as $row) {
            if ($row->score == 90)
                $row->score = 80;
            else if ($row->score == 80)
                $row->score = 60;

            DB::connection()->table('memorization_details')->insert((array)$row);
        }
    }
}
