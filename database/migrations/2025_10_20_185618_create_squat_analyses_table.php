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
        Schema::create('squat_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_analysis_id')->constrained()->cascadeOnDelete();
            // --- Información General (Resumen) ---
            $table->unsignedTinyInteger('total_reps')->nullable();
            // Promedios
            $table->decimal('avg_min_knee_angle', 6, 2)->nullable(); // Usamos el nombre del API
            $table->decimal('avg_efficiency_pct', 6, 2)->nullable();
            $table->decimal('avg_rms_px', 8, 2)->nullable(); // Promedio de RMS (desviación horizontal)
            // Feedback
            $table->string('depth_label', 50)->nullable();
            $table->text('depth_message')->nullable(); // Necesita ser TEXT o STRING más largo

            // Mejor/Peor Repetición (Bar Path)
            $table->unsignedTinyInteger('best_rep_num')->nullable();
            $table->decimal('best_efficiency_pct', 6, 2)->nullable();
            $table->unsignedTinyInteger('worst_rep_num')->nullable();
            $table->decimal('worst_efficiency_pct', 6, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squat_analyses');
    }
};
