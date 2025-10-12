<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'movement',
        'analyzed_at',
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
}
