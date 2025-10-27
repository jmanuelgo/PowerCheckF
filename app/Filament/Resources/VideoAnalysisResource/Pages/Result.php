<?php

namespace App\Filament\Resources\VideoAnalysisResource\Pages;

use App\Filament\Resources\VideoAnalysisResource;
use App\Models\VideoAnalysis;
use Filament\Resources\Pages\Page;

class Result extends Page
{
    protected static string $resource = VideoAnalysisResource::class;
    protected static string $view = 'filament.video.result';
    public VideoAnalysis $record;
    public array $metrics = [];
    public array $summary = [];


    public function mount(VideoAnalysis $record): void
    {
        $this->record = $record;
        if ($record->movement === 'squat') {
            $analysis = $record->squatAnalysis;

            if ($analysis) {
                $metricsCollection = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();
                $this->metrics = $metricsCollection->toArray();
                $this->summary = [
                    'total_reps'                  => $analysis->total_reps,
                    'depth_label'                 => $analysis->depth_label,
                    'avg_min_knee_angle'          => $analysis->avg_min_knee_angle,
                    'avg_efficiency_pct'          => $analysis->avg_efficiency_pct,
                    'avg_horizontal_deviation_px' => $analysis->avg_rms_px,
                    'depth_message'               => $analysis->depth_message,
                ];
            }
        } elseif ($record->movement === 'deadlift') {
            $analysis = $record->deadliftAnalysis;
            if ($analysis) {
                $metricsCollection = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();
                $this->metrics = $metricsCollection->toArray();
                $this->summary = [
                    'total_reps'                  => $analysis->total_reps,
                    'avg_efficiency_pct'          => $analysis->avg_efficiency_pct,
                    'avg_horizontal_deviation_px' => $analysis->avg_shoulder_bar_deviation_px,
                    'summary_message'             => $analysis->summary_message,
                ];
            }
        } elseif ($record->movement === 'bench') {
            $analysis = $record->benchPressAnalysis;
            if ($analysis) {
                $this->summary = [
                    'total_reps'       => $analysis->total_reps,
                    'avg_efficiency_pct' => $analysis->avg_score,
                    'best_rep_score'   => $analysis->best_rep_score,
                ];
            }
        }
        if ($analysis) {
            $metricsCollection = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();
            $this->metrics = $metricsCollection->toArray();
        }
    }

    private function getDepthMessage(?string $label): string
    {
        switch (strtolower($label ?? '')) {
            case 'profunda':
                return 'Es una sentadilla profunda. Vigila neutro lumbar y rodillas siguiendo la punta del pie.';
            case 'paralela':
                return 'Has alcanzado una buena profundidad paralela. ¡Sigue así!';
            case 'parcial':
                return 'La sentadilla es parcial. Intenta bajar un poco más para maximizar la activación.';
            default:
                return '';
        }
    }
}
