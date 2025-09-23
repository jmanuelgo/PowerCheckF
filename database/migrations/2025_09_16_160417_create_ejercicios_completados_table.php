<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ejercicios_completados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ejercicio_dia_id')->constrained('ejercicios_dia')->onDelete('cascade');
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_completado')->nullable();
            $table->tinyInteger('porcentaje_series_completadas')->default(0);
            $table->text('notas_atleta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ejercicios_completados');
    }
};
