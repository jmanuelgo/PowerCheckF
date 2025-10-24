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
        Schema::create('deadlift_rep_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deadlift_analysis_id')
                ->constrained()
                ->cascadeOnDelete();

            // --- Datos de la Repetición ---
            $table->unsignedTinyInteger('rep_number');

            // Métricas de Trayectoria de Barra (Bar Path)
            $table->decimal('path_length_px', 8, 2)->nullable();
            $table->decimal('vertical_range_px', 8, 2)->nullable();
            $table->decimal('excess_path_px', 8, 2)->nullable();
            $table->decimal('efficiency_pct', 6, 2)->nullable();
            $table->decimal('rms_px', 8, 2)->nullable();
            $table->decimal('tilt_deg', 6, 2)->nullable();

            $table->decimal('avg_shoulder_bar_deviation_px', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadlift_rep_metrics');
    }
};
