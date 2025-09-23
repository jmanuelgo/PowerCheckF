<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dias_entrenamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semana_rutina_id')->constrained('semanas_rutina')->onDelete('cascade');
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']);
            $table->boolean('completado_por_atleta')->default(false);
            $table->timestamp('fecha_completado')->nullable();
            $table->tinyInteger('porcentaje_completado')->default(0);
            $table->text('notas_atleta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dias_entrenamiento');
    }
};
