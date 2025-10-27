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
        Schema::create('bench_press_rep_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bench_press_analysis_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('rep_number');
            $table->decimal('score_general', 8, 2)->nullable();
            $table->decimal('curvatura_j_px', 8, 2)->nullable();
            $table->decimal('rectitud_bajada_rmse', 8, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bench_press_rep_metrics');
    }
};
