<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = 'ejercicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'grupo_muscular',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ejerciciosDia()
    {
        return $this->hasMany(EjercicioDia::class);
    }
}
