<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EjercicioCompletado extends Model
{
    use HasFactory;

    protected $table = 'ejercicios_completados';

    protected $fillable = [
        'ejercicio_dia_id',
        'completado',
        'fecha_completado',
        'porcentaje_series_completadas',
        'notas_atleta'
    ];

    protected $casts = [
        'completado' => 'boolean',
        'fecha_completado' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ejercicioDia()
    {
        return $this->belongsTo(EjercicioDia::class, 'ejercicio_dia_id');
    }

    public function seriesRealizadas()
    {
        return $this->hasMany(SerieRealizada::class, 'ejercicio_completado_id');
    }
}
