<?php

namespace App\Filament\Resources\DeviceMetricResource\Pages;

use App\Filament\Resources\DeviceMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ListDeviceMetrics extends ListRecords
{
    protected static string $resource = DeviceMetricResource::class;

    protected function getHeaderActions(): array
    {
        // Quitamos el botón de crear
        return [];
    }

    // Aquí aplicamos el filtro de permisos
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = Auth::user();

        // Si el usuario NO PUEDE ver todas las métricas, filtramos por su ID.
        if ($user && !Gate::allows('view_any_device::metric')) {
            $query->where('athlete_id', $user->id);
        }

        return $query;
    }
}