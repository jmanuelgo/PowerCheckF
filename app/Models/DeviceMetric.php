<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceMetric extends Model
{
    protected $table = 'device_metrics';

    // 🚫 Evita error 500 si tu tabla NO tiene created_at/updated_at
    public $timestamps = false;

    // Mass assignment permitido
    protected $fillable = [
        'device_id',
        'athlete_id',
        'bpm',
        'repeticiones',
        'ejercicio',
        // 'captured_at',  // si la columna existe y la quieres asignar
    ];

    protected $casts = [
        'bpm'          => 'float',
        'repeticiones' => 'integer',
        'captured_at'   => 'datetime', // si existe la columna
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }
}