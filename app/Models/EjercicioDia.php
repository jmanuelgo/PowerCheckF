<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EjercicioDia extends Model
{
    use HasFactory;

    protected $table = 'ejercicios_dia';

    protected $fillable = [
        'dia_entrenamiento_id',
        'ejercicio_id',
        'orden',
        'notas',
    ];

    public function diaEntrenamiento()
    {
        return $this->belongsTo(DiaEntrenamiento::class, 'dia_entrenamiento_id');
    }

    public function seriesEjercicio()
    {
        return $this->hasMany(SerieEjercicio::class, 'ejercicio_dia_id');
    }

    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class);
    }

    public function ejerciciosCompletados()
    {
        return $this->hasMany(EjercicioCompletado::class, 'ejercicio_dia_id');
    }

    public function series()
    {
        return $this->hasMany(\App\Models\SerieEjercicio::class, 'ejercicio_dia_id');
    }
}
