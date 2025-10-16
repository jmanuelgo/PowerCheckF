<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder; // Importante

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        // Los resources por defecto tienen un botón de "Crear".
        // Lo quitamos porque los dispositivos se registran solos.
        return [];
    }

    // Añadimos este método para ordenar por defecto
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->orderByDesc('last_seen');
    }
}