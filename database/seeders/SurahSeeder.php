<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surahs = DB::connection('al_quran_db')->table('table_surah')->get();
        foreach ($surahs as $surah) {
            DB::connection()->table('surahs')->insert([
                'id' => $surah->Surah,
                'name' => $surah->Ayat,
                'total_ayahs' => $surah->Jumlah_Ayat,
            ]);
        }
    }
}
