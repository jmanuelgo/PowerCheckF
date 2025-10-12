<?php

namespace App\Filament\Resources\VideoAnalysisResource\Pages;

use App\Filament\Resources\VideoAnalysisResource;
use App\Models\VideoAnalysis;
use Filament\Resources\Pages\Page;

class PickFull extends Page
{
    protected static string $resource = VideoAnalysisResource::class;
    protected static string $view = 'filament.video.pick-full';

    public VideoAnalysis $record;

    public function mount(VideoAnalysis $record): void
    {
        $this->record = $record;
    }
}
