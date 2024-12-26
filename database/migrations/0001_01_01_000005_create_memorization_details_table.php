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
        Schema::create('memorization_details', function (Blueprint $table) {
            $table->foreignId('memorization_id')->constrained()->onDelete('cascade');
            $table->foreignId('ayah_id')->constrained()->onDelete('restrict');
            $table->unsignedTinyInteger('score')->nullable()->defaut(null);
            $table->text('notes')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorization_details');
    }
};
