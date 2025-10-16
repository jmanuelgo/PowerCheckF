<?php

namespace App\Filament\Resources\DeviceMetricResource\Pages;

use App\Filament\Resources\DeviceMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeviceMetric extends EditRecord
{
    protected static string $resource = DeviceMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
