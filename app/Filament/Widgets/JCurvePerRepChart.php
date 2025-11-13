<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VideoAnalysis;
use App\Models\BenchPressAnalysis;


class JCurvePerRepChart extends ChartWidget
{
    protected static ?string $heading = 'Analisis de Curva "J" por Repetición';

    public ?VideoAnalysis $record = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        if(!$this->record || $this->record->movement !== 'bench') {
            return [];
        }


        $analysis = $this->record->benchPressAnalysis;
        if(!$analysis) {
            return [];
        }

        $metrics = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();

        if($metrics->isEmpty()) {
            return [];
        }

        $labels = $metrics->pluck('rep_number')->map(fn($rep) => "Rep {$rep}")->all();

        $data = $metrics->pluck('curvatura_j_px')->all();

        return [
            'datasets' => [
                [
                    'label' => 'J-Curve (px)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192)',
                    'tension' => 0.2,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
