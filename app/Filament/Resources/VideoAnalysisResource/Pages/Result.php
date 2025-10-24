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

        // Si el movimiento es 'squat', cargamos los datos normalizados
        if ($record->movement === 'squat') {

            // 1. Obtener el registro de resumen (relación 1:1, singular)
            $analysis = $record->squatAnalysis;

            if ($analysis) {
                // 2. Obtener las métricas por repetición (relación 1:M a través del resumen)
                $metricsCollection = $analysis->repMetrics()->orderBy('rep_number', 'asc')->get();

                // Mapeo de datos para la vista
                $this->metrics = $metricsCollection->toArray();

                // Mapeo del resumen (directamente de la DB)
                $this->summary = [
                    'total_reps'                  => $analysis->total_reps,
                    'depth_label'                 => $analysis->depth_label,
                    'avg_min_knee_angle'          => $analysis->avg_min_knee_angle,
                    'avg_efficiency_pct'          => $analysis->avg_efficiency_pct,
                    'avg_horizontal_deviation_px' => $analysis->avg_rms_px, // Usamos avg_rms_px del DB
                    'depth_message'               => $analysis->depth_message, // Usamos el mensaje guardado en DB
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
        }
    }

    // Función de ayuda opcional para generar mensajes
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
