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


            $t->enum('movement', ['squat', 'bench', 'deadlift']);
            $t->timestamp('analyzed_at')->nullable();
            $t->decimal('efficiency_pct', 6, 2)->nullable();


            $t->string('status')->default('processing');
            $t->string('job_id')->nullable();
            $t->string('download_url')->nullable();
            $t->string('frame_url')->nullable();
            $t->json('raw_metrics')->nullable();

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
