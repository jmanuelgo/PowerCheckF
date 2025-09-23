<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('series_realizadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serie_ejercicio_id')->constrained('series_ejercicio')->onDelete('cascade');
            $table->foreignId('ejercicio_completado_id')->constrained('ejercicios_completados')->onDelete('cascade');
            $table->tinyInteger('repeticiones_realizadas');
            $table->decimal('peso_realizado', 5, 2)->nullable();
            $table->boolean('completada')->default(false);
            $table->text('notas')->nullable();
            $table->timestamp('fecha_realizacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('series_realizadas');
    }
};
