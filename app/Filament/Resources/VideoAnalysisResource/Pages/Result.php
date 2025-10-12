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
    public ?array $summary = null;

    public function mount(VideoAnalysis $record): void
    {
        $this->record = $record;

        // Si guardas JSON en columnas -> decódelas; maneja nulos con cuidado
        $this->metrics = is_array($record->raw_metrics)
            ? $record->raw_metrics
            : (filled($record->raw_metrics) ? (array) json_decode($record->raw_metrics, true) : []);

        $this->summary = is_array($record->summary ?? null)
            ? $record->summary
            : (filled($record->summary ?? null) ? (array) json_decode($record->summary, true) : null);
    }
}
