<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'dias_entrenamiento';

    protected $fillable = [
        'semana_rutina_id',
        'dia_semana',
        'completado_por_atleta',
        'fecha_completado',
        'porcentaje_completado',
        'notas_atleta'
    ];

    protected $casts = [
        'completado_por_atleta' => 'boolean',
        'fecha_completado' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function semanaRutina()
    {
        return $this->belongsTo(SemanaRutina::class, 'semana_rutina_id');
    }

    public function ejerciciosDia()
    {
        return $this->hasMany(EjercicioDia::class, 'dia_entrenamiento_id');
    }

    public function ejerciciosCompletados()
    {
        return $this->hasManyThrough(EjercicioCompletado::class, EjercicioDia::class);
    }
}
