<?php

namespace App\Filament\Widgets;

use App\Models\VideoAnalysis;
use Filament\Widgets\Widget;
use App\Models\SquatRepMetric;
use App\Models\DeadliftRepMetric;
use App\Models\BenchPressRepMetric;


class AnalysisComparison extends Widget
{
    protected static string $view = 'filament.widgets.analysis-comparison';

    // Para recibir el registro del análisis
    public ?VideoAnalysis $record = null;

    public ?array $bestRepMetric = null;
    public ?array $worstRepMetric = null;

    public function mount(): void
    {
        if (!$this->record) {
            return;
        }

        $analysisSummary = null;
        $metricModelClass = null;

        if ($this->record->movement === 'squat') {
            $analysisSummary = $this->record->squatAnalysis;
            $metricModelClass = SquatRepMetric::class;
        } elseif ($this->record->movement === 'deadlift') {
            $analysisSummary = $this->record->deadliftAnalysis;
            $metricModelClass = DeadliftRepMetric::class;
        } elseif ($this->record->movement === 'bench') {
            $analysisSummary = $this->record->benchPressAnalysis;
            $metricModelClass = BenchPressRepMetric::class;
        }
        $bestRepKey = 'best_rep_num';
        $worstRepKey = 'worst_rep_num';
        if ($this->record->movement === 'bench') {
            $bestRepKey = 'best_rep_num';
            $worstRepKey = 'worst_rep_num';
        }

        if ($analysisSummary && $analysisSummary->best_rep_num && $analysisSummary->worst_rep_num) {
            $bestMetric = $metricModelClass::where($analysisSummary->getForeignKey(), $analysisSummary->id)
                ->where('rep_number', $analysisSummary->best_rep_num)
                ->first();

            $worstMetric = $metricModelClass::where($analysisSummary->getForeignKey(), $analysisSummary->id)
                ->where('rep_number', $analysisSummary->worst_rep_num)
                ->first();

            if ($bestMetric && $worstMetric) {
                // Convertimos los modelos a arrays para pasarlos a la vista
                $this->bestRepMetric = $bestMetric->toArray();
                $this->worstRepMetric = $worstMetric->toArray();
            }
        }
    }

    public function getColumnSpan(): int | string | array
    {
        return $this->bestRepMetric && $this->worstRepMetric ? 'full' : 0;
    }
}
