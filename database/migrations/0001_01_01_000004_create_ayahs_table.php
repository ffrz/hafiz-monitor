<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surah_id')->constrained()->onDelete('restrict');
            $table->unsignedSmallInteger('number');
            $table->unsignedTinyInteger('juz')->nullable()->default(null);
            // $table->float('word_score_point', 2)->nullable()->default(null); // jumlah kata dalam ayat
            // $table->float('line_score_point', 2)->nullable()->default(null); // jumlah baris, misal 1,2 baris, 0,2 baris
            $table->float('score_weight', 2)->nullable()->default(null); // bobot akhir berdasarkan kriteria, saat ini menggunakan jumlah kata
            $table->text('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ayahs');
    }
};
