<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AyahSeeder extends Seeder
{
    // kode ini hasil generate chat gpt, BELUM SEMPAT DIVALIDASI!!!
    protected $juzMapping = [
        1 => ['start' => [1, 1], 'end' => [2, 141]],
        2 => ['start' => [2, 142], 'end' => [2, 252]],
        3 => ['start' => [2, 253], 'end' => [3, 92]],
        4 => ['start' => [3, 93], 'end' => [4, 23]],
        5 => ['start' => [4, 24], 'end' => [4, 147]],
        6 => ['start' => [4, 148], 'end' => [5, 81]],
        7 => ['start' => [5, 82], 'end' => [6, 110]],
        8 => ['start' => [6, 111], 'end' => [7, 87]],
        9 => ['start' => [7, 88], 'end' => [8, 40]],
        10 => ['start' => [8, 41], 'end' => [9, 92]],
        11 => ['start' => [9, 93], 'end' => [11, 5]],
        12 => ['start' => [11, 6], 'end' => [12, 52]],
        13 => ['start' => [12, 53], 'end' => [14, 52]],
        14 => ['start' => [15, 1], 'end' => [16, 128]],
        15 => ['start' => [17, 1], 'end' => [18, 74]],
        16 => ['start' => [18, 75], 'end' => [20, 135]],
        17 => ['start' => [21, 1], 'end' => [22, 78]],
        18 => ['start' => [23, 1], 'end' => [25, 20]],
        19 => ['start' => [25, 21], 'end' => [27, 55]],
        20 => ['start' => [27, 56], 'end' => [29, 45]],
        21 => ['start' => [29, 46], 'end' => [33, 30]],
        22 => ['start' => [33, 31], 'end' => [36, 27]],
        23 => ['start' => [36, 28], 'end' => [39, 31]],
        24 => ['start' => [39, 32], 'end' => [41, 46]],
        25 => ['start' => [41, 47], 'end' => [45, 37]],
        26 => ['start' => [46, 1], 'end' => [51, 30]],
        27 => ['start' => [51, 31], 'end' => [57, 29]],
        28 => ['start' => [58, 1], 'end' => [66, 12]],
        29 => ['start' => [67, 1], 'end' => [77, 50]],
        30 => ['start' => [78, 1], 'end' => [114, 6]],
    ];

    protected function getJuz($surah, $ayah)
    {
        foreach ($this->juzMapping as $juz => $range) {
            list($startSurah, $startAyah) = $range['start'];
            list($endSurah, $endAyah) = $range['end'];

            // Check if the Surah and Ayah are within the range for the Juz
            if (($surah > $startSurah || ($surah == $startSurah && $ayah >= $startAyah)) &&
                ($surah < $endSurah || ($surah == $endSurah && $ayah <= $endAyah))
            ) {
                return $juz;
            }
        }

        return null; // If not found
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ayahs = DB::connection('al_quran_db')->table('table_ayat')->get();
        foreach ($ayahs as $ayah) {
            $text = $ayah->Arab;
            if ($ayah->Ayat == 1 && $ayah->Surah > 1) {
                $text = trim(str_replace('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', '', $text));
            }

            DB::connection()->table('ayahs')->insert([
                'surah_id' => $ayah->Surah,
                'number' => $ayah->Ayat,
                'juz' => static::getJuz($ayah->Surah, $ayah->Ayat),
                'text' => $text,
            ]);
        }
    }
}
