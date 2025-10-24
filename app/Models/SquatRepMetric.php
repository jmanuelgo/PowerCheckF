<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquatRepMetric extends Model
{
    use HasFactory;

    protected $table = 'squat_rep_metrics';

    protected $fillable = [
        'squat_analysis_id',
        'rep_number',
        'min_knee_angle',
        'path_length_px',
        'vertical_range_px',
        'excess_path_px',
        'efficiency_pct',
        'rms_px',
        'tilt_deg',
    ];

    public function squatAnalysis(): BelongsTo
    {
        return $this->belongsTo(SquatAnalysis::class);
    }
}
