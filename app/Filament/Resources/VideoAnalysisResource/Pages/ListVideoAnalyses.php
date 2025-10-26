<?php

namespace App\Filament\Resources\VideoAnalysisResource\Pages;

use App\Filament\Resources\VideoAnalysisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;
use App\Filament\Pages\Video\UploadSquat;
use App\Filament\Pages\Video\UploadBench;
use App\Filament\Pages\Video\UploadDeadlift;

class ListVideoAnalyses extends ListRecords
{
    protected static string $resource = VideoAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('analizar_squat')
                ->label('Analizar: Sentadilla')
                ->icon('heroicon-o-video-camera')
                ->color('primary')
                ->url(\App\Filament\Pages\Video\UploadSquat::getUrl()),

            \Filament\Actions\Action::make('analizar_bench')
                ->label('Analizar: Press banca')
                ->icon('heroicon-o-video-camera')
                ->color('warning')
                ->url(UploadBench::getUrl()),

            \Filament\Actions\Action::make('analizar_deadlift')
                ->label('Analizar: Peso muerto')
                ->icon('heroicon-o-video-camera')
                ->color('info')
                ->url(UploadDeadlift::getUrl()),
        ];
    }
}
