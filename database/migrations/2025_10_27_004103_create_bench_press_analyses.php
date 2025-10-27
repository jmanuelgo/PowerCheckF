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
        Schema::create('bench_press_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_analysis_id')->constrained()->cascadeOnDelete();

            // --- Resumen ---
            $table->unsignedTinyInteger('total_reps');
            $table->decimal('avg_score', 8, 2)->nullable();

            // --- Mejor/Peor Rep (para el widget) ---
            $table->unsignedTinyInteger('best_rep_num')->nullable();
            $table->decimal('best_rep_score', 8, 2)->nullable();
            $table->unsignedTinyInteger('worst_rep_num')->nullable();
            $table->decimal('worst_rep_score', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bench_press_analyses');
    }
};
