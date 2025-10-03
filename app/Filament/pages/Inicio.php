<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\AtletasDelEntrenadorStats;
use App\Filament\Pages\MiRutinaDeHoy;



class Inicio extends Dashboard
{
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $navigationIcon  = 'heroicon-o-home';

    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        $user = Auth::user(); // 👈 así desaparece el warning del linter

        // Si usas spatie/laravel-permission:
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
        return [
            AtletasDelEntrenadorStats::class,
        ];
    }
}
