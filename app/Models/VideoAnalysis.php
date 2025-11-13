<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SquatAnalysis;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\DeadliftAnalysis;
use App\Models\User;

class VideoAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'movement',
        'analyzed_at',
        'weight',
        'efficiency_pct',
        'status',
        'job_id',
        'download_url',
        'frame_url',
        'raw_metrics',
    ];

    protected $casts = [
        'analyzed_at' => 'datetime',
        'raw_metrics' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function squatAnalysis(): HasOne
    {
        return $this->hasOne(SquatAnalysis::class);
    }

    public function deadliftAnalysis(): HasOne
    {
        return $this->hasOne(DeadliftAnalysis::class);
    }
    public function benchPressAnalysis(): HasOne
    {
        return $this->hasOne(BenchPressAnalysis::class);
    }
}
