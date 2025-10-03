<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemanaRutina extends Model
{
    use HasFactory;

    protected $table = 'semanas_rutina';

    protected $fillable = [
        'rutina_id',
        'numero_semana'
    ];

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }

    public function diasEntrenamiento()
    {
        return $this->hasMany(DiaEntrenamiento::class, 'semana_rutina_id');
    }
    public function dias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\DiaEntrenamiento::class, 'semana_rutina_id')
            ->orderByRaw("FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')");
    }
}
