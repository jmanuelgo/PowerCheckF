<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('device_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->unsignedBigInteger('athlete_id');
            $table->float('bpm')->nullable();
            $table->integer('repeticiones')->default(0);
            $table->string('exercise', 50)->nullable();
            $table->timestamp('captured_at')->useCurrent(); 

            $table->index(['device_id', 'athlete_id']);
            $table->index('ejercicio');
        });
    }
    public function down(): void {
        Schema::dropIfExists('device_metrics');
        Schema::table('device_metrics', function (Blueprint $table) {
        if (Schema::hasColumn('device_metrics', 'exercise')) {
            $table->dropColumn('exercise');
        }
        if (Schema::hasColumn('device_metrics', 'captured_at')) {
            $table->dropColumn('captured_at');
        }
    });
    }

};