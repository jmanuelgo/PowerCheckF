<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BenchPressAnalysis extends Model
{
    protected $table = 'bench_press_analyses';
    protected $fillable = [
        'video_analysis_id',
        'total_reps',
        'avg_score',
        'best_rep_num',
        'best_rep_score',
        'worst_rep_num',
        'worst_rep_score',
    ];
    public function videoAnalysis(): BelongsTo
    {
        return $this->belongsTo(VideoAnalysis::class);
    }
    public function repMetrics(): HasMany
    {
        return $this->hasMany(BenchPressRepMetric::class);
    }
}
