<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Memorization;
use App\Models\MemorizationDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AppUtilsController extends Controller
{
    /**
     * Function to count words in an Arabic sentence
     *
     * @param string $text The Arabic sentence
     * @return int The word count
     */
    private function _wordCount($text)
    {
        // Daftar tanda baca dalam bahasa Arab yang ingin dihapus
        $punctuations = ['ۛ', 'ۖ', 'ۗ', 'ۚ', 'ۙ', 'ۘ', '۩', 'ۜ'];

        // Menghapus tanda baca dari teks
        $filteredText = str_replace($punctuations, '', $text);

        // Remove diacritics, punctuation, and special characters (like ۚ, ۖ)
        $cleanedText = $this->_removeHarkat($filteredText);

        // Normalize spaces
        $normalizedText = trim(preg_replace('/\s+/', ' ', $cleanedText));

        // Split the text by spaces to count words
        $words = explode(' ', $normalizedText);

        // Return the count of words
        return count($words);
    }

    private function _removeHarkat($text)
    {
        // Pola regex untuk mencocokkan harkat (tanda baca) di teks Arab
        $pattern = '/[\x{064B}-\x{0652}\x{0670}\x{064F}\x{064E}\x{064D}\x{064C}\x{0651}\x{0650}\x{064A}\x{0653}\x{0654}\x{0655}\x{0656}\x{0657}\x{0658}\x{0659}\x{065A}\x{065B}\x{065C}\x{065D}\x{065E}\x{065F}\x{0660}-\x{0669}\x{0670}]/u';

        // Menghapus harkat dengan menggantinya menjadi string kosong
        $cleanedText = preg_replace($pattern, '', $text);

        return $cleanedText;
    }

    private function _buildAyahScoreWeight()
    {
        $ayahs = Ayah::all();
        DB::beginTransaction();
        foreach ($ayahs as $ayah) {
            $ayah->score_weight = $this->_wordCount($ayah->text);
            $ayah->save();
        }
        DB::commit();
    }

    private function _rebuildScore()
    {
        $sessions = Memorization::with('details', 'details.ayah')->get();

        DB::beginTransaction();
        foreach ($sessions as $session) {
            $totalScore = 0;
            $totalWeight = 0;
            $details = $session->details;
            foreach ($details as $detail) {
                $score = $detail->score * $detail->ayah->score_weight;
                DB::update("UPDATE memorization_details
                            SET weighted_score=$score
                            WHERE memorization_id=$session->id
                                AND ayah_id=$detail->ayah_id");
                $detail->save();
                $totalScore += $score;
                $totalWeight += $detail->ayah->score_weight;
            }
            if ($totalWeight > 0) {
                $session->score = $totalScore / $totalWeight;
                $session->save();
            }
        }
        DB::commit();
    }

    public function run()
    {
        if (!env('APP_CMD_ALLOWED')) {
            abort(403, 'Acces denied!');
        }

        $this->_buildAyahScoreWeight();
        $this->_rebuildScore();

        dd('SUCCESS');
    }
}
