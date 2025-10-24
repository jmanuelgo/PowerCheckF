<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\VideoAnalysis;
use App\Models\DeadliftRepMetric;

class DeadliftAnalysis extends Model
{
    protected $table = 'deadlift_analyses';

    protected $fillable = [
        'video_analysis_id',
        'total_reps',
        'avg_efficiency_pct',
        'avg_shoulder_bar_deviation_px',
        'summary_label',
        'summary_message',
        'best_rep_num',
        'best_efficiency_pct',
        'worst_rep_num',
        'worst_efficiency_pct',
    ];

    public function repMetrics(): HasMany
    {
        return $this->hasMany(DeadliftRepMetric::class);
    }
    public function videoAnalysis(): BelongsTo
    {
        return $this->belongsTo(VideoAnalysis::class);
    }
}
