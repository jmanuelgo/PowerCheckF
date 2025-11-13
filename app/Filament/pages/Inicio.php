<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\AtletasDelEntrenadorStats;
use App\Filament\Pages\MiRutinaDeHoy;
use App\Filament\Widgets\RutinasRecientesTable;



class Inicio extends Dashboard
{
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $navigationIcon  = 'heroicon-o-home';

    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        $user = Auth::user();

        $role = method_exists($user, 'getRoleNames')
            ? ($user->getRoleNames()->first() ?? 'Usuario')
            : 'Usuario';

        $map = [
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'entrenador'  => 'Entrenador',
            'atleta'      => 'Atleta',
        ];

        $roleText = $map[$role] ?? Str::headline($role);

        return "Inicio - {$roleText}";
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        $user = Auth::user();

        if ($user->hasRole('entrenador')) {
            return [
                \App\Filament\Widgets\AtletasDelEntrenadorStats::class,
                RutinasRecientesTable::class,
            ];
        }

        if ($user->hasRole('atleta')) {
            return [
                \App\Filament\Widgets\RutinaPendienteDeHoyWidget::class,
            ];
        }

        return [];
    }
}
