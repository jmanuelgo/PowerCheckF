<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeadliftRepMetric extends Model
{

    protected $table = 'deadlift_rep_metrics';

    protected $fillable = [
        'deadlift_analysis_id',
        'rep_number',
        'path_length_px',
        'vertical_range_px',
        'excess_path_px',
        'efficiency_pct',
        'rms_px',
        'tilt_deg',
        'avg_shoulder_bar_deviation_px',
    ];
    public function deadliftAnalysis(): BelongsTo
    {
        return $this->belongsTo(DeadliftAnalysis::class);
    }
}
