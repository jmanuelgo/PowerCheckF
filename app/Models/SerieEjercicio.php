<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerieEjercicio extends Model
{
    use HasFactory;

    protected $table = 'series_ejercicio';

    protected $fillable = [
        'ejercicio_dia_id',
        'numero_serie',
        'repeticiones_objetivo',
        'peso_objetivo',
        'descanso_segundos'
    ];

    protected $casts = [
        'peso_objetivo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ejercicioDia()
    {
        return $this->belongsTo(EjercicioDia::class, 'ejercicio_dia_id');
    }

    public function seriesRealizadas()
    {
        return $this->hasMany(SerieRealizada::class, 'serie_ejercicio_id');
    }
}
