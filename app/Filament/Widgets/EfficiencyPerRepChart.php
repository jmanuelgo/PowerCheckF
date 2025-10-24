<?php

namespace App\Filament\Widgets;

use App\Models\VideoAnalysis;
use Filament\Widgets\ChartWidget;

class EfficiencyPerRepChart extends ChartWidget
{
    protected static ?string $heading = 'Eficiencia por Repetición';

    public ?VideoAnalysis $record = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        if (!$this->record) {
            return [];
        }

        $analysis = null;
        if ($this->record->movement === 'squat') {
            $analysis = $this->record->squatAnalysis;
        } elseif ($this->record->movement === 'deadlift') {
            $analysis = $this->record->deadliftAnalysis;
        }

        if (!$analysis) {
            return [];
        }
        $metrics = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();

        if ($metrics->isEmpty()) {
            return [];
        }

        $labels = $metrics->pluck('rep_number')->map(fn($rep) => "Rep {$rep}")->all();
        $data = $metrics->pluck('efficiency_pct')->all();

        return [
            'datasets' => [
                [
                    'label' => 'Eficiencia (%)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'tension' => 0.2,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
