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
        Schema::create('deadlift_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_analysis_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('total_reps')->nullable();
            $table->decimal('avg_efficiency_pct', 6, 2)->nullable();
            $table->decimal('avg_shoulder_bar_deviation_px', 8, 2)->nullable();
            $table->string('summary_label', 100)->nullable();
            $table->text('summary_message')->nullable();
            // Mejor/Peor Repetición (Basado en Eficiencia)
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
        Schema::dropIfExists('deadlift_analyses');
    }
};
