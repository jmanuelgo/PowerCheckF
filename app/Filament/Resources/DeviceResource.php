<?php

namespace App\Filament\Resources;

use App\Models\Device;
use Filament\Resources\Resource;
use App\Filament\Resources\DeviceResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class DeviceResource extends Resource implements HasShieldPermissions
{
    // El modelo del recurso (obligatorio)
    protected static ?string $model = Device::class;

    // Forzamos el slug para que la ruta sea ...resources.devices.*
    protected static ?string $slug = 'devices';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // 👈 oculta "Device" del sidebar
    }
    // (Opcional) icono y label en el sidebar
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Devices';

    // Tus permisos personalizados (Shield v3)
    public static function getPermissionPrefixes(): array
    {
        return [
            'view', 'view_any', 'create', 'update', 'delete', 'delete_any',
            'view_available', 'connect', 'disconnect', 'force_release',
        ];
    }

    // PÁGINAS DEL RECURSO -> esto crea las rutas (index/create/edit)
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit'   => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}