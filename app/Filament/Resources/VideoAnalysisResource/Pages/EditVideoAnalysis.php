<?php

namespace App\Filament\Resources\VideoAnalysisResource\Pages;

use App\Filament\Resources\VideoAnalysisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideoAnalysis extends EditRecord
{
    protected static string $resource = VideoAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
