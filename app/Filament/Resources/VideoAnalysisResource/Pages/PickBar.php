<?php

namespace App\Filament\Resources\VideoAnalysisResource\Pages;

use App\Filament\Resources\VideoAnalysisResource;
use App\Models\VideoAnalysis;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Http;


class PickBar extends Page
{
    protected static string $resource = VideoAnalysisResource::class;
    protected static string $view = 'filament.video.pick-bar';

    public VideoAnalysis $record;

    public function mount(VideoAnalysis $record): void
    {
        $this->record = $record; // <- el blade usará $record
    }
}
