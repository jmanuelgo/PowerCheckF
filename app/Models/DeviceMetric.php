<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceMetric extends Model
{
    protected $table = 'device_metrics';
    protected $fillable = ['device_id', 'athlete_id', 'bpm', 'repeticiones','ejercicio', ];

protected $casts = [
        'bpm'         => 'float',
        'repeticiones'=> 'integer',

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