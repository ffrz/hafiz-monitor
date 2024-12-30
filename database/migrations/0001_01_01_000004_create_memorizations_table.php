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
        Schema::create('memorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hafiz_id')->constrained('hafizes')->onDelete('cascade');
            $table->unsignedTinyInteger('start_surah_id')->nullable();
            $table->unsignedTinyInteger('end_surah_id')->nullable();
            $table->string('title')->nullable()->default('');
            $table->unsignedTinyInteger('score')->default(0);
            $table->text('notes')->nullable()->default(null);
            $table->enum('status', ['open', 'closed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorizations');
    }
};
