<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class BenchPressRepMetric extends Model
{
    protected $table = 'bench_press_rep_metrics';
    protected $fillable = [
        'bench_press_analysis_id',
        'rep_number',
        'score_general',
        'curvatura_j_px',
        'rectitud_bajada_rmse',
        'observacion',
    ];
    public function benchPressAnalysis(): BelongsTo
    {
        return $this->belongsTo(BenchPressAnalysis::class);
    }
}
