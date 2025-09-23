<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerieRealizada extends Model
{
    use HasFactory;

    protected $table = 'series_realizadas';

    protected $fillable = [
        'serie_ejercicio_id',
        'ejercicio_completado_id',
        'repeticiones_realizadas',
        'peso_realizado',
        'completada',
        'notas',
        'fecha_realizacion'
    ];

    protected $casts = [
        'peso_realizado' => 'decimal:2',
        'completada' => 'boolean',
        'fecha_realizacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function serieEjercicio()
    {
        return $this->belongsTo(SerieEjercicio::class, 'serie_ejercicio_id');
    }

    public function ejercicioCompletado()
    {
        return $this->belongsTo(EjercicioCompletado::class, 'ejercicio_completado_id');
    }
}
