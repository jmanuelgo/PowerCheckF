<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('series_ejercicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ejercicio_dia_id')->constrained('ejercicios_dia')->onDelete('cascade');
            $table->tinyInteger('numero_serie');
            $table->tinyInteger('repeticiones_objetivo');
            $table->decimal('peso_objetivo', 5, 2)->nullable();
            $table->integer('descanso_segundos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('series_ejercicio');
    }
};
