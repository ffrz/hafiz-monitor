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
        Schema::create('hafiz_memorized_juzs', function (Blueprint $table) {
            $table->foreignId('hafiz_id')->constrained('hafizes', 'id')->onDelete('cascade');
            $table->unsignedTinyInteger('juz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafiz_memorized_juzs');
    }
};
