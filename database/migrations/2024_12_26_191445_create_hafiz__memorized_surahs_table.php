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
        Schema::create('hafiz_memorized_surahs', function (Blueprint $table) {
            $table->foreignId('hafiz_id')->constrained('hafizes', 'id')->onDelete('cascade');
            $table->foreignId('surah_id')->constrained('surahs', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafiz_memorized_surahs');
    }
};
