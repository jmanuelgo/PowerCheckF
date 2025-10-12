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
        Schema::create('video_analyses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Lo que te interesa mostrar:
            $t->enum('movement', ['squat', 'bench', 'deadlift']);      // ejercicio
            $t->timestamp('analyzed_at')->nullable();                 // fecha análisis (cuando termina)
            $t->decimal('efficiency_pct', 6, 2)->nullable();          // eficiencia del recorrido (promedio)

            // Campos mínimos para completar el flujo con la API:
            $t->string('status')->default('processing');              // processing|need_pick|need_pick_full|done|failed
            $t->string('job_id')->nullable();
            $t->string('download_url')->nullable();
            $t->string('frame_url')->nullable();                      // frame para “pick”
            $t->json('raw_metrics')->nullable();                      // guardar el JSON crudo (opcional)

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_analyses');
    }
};
