<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\VideoAnalysis;
use App\Models\SquatRepMetric;


class SquatAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_analysis_id',
        'total_reps',
        'avg_min_knee_angle',
        'avg_efficiency_pct',
        'avg_rms_px',
        'depth_label',
        'depth_message',
        'best_rep_num',
        'best_efficiency_pct',
        'worst_rep_num',
        'worst_efficiency_pct',
    ];

    public function videoAnalysis(): BelongsTo
    {
        return $this->belongsTo(VideoAnalysis::class);
    }
    public function repMetrics(): HasMany
    {
        return $this->hasMany(SquatRepMetric::class);
    }
}
