<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AtletasDelEntrenadorStats extends BaseWidget
{
    protected ?string $heading = 'Mis Atletas';
    public static function canView(): bool
    {
        $userId = Auth::id();
        return $userId && DB::table('entrenadors')->where('user_id', $userId)->exists();
    }
    protected function getStats(): array
    {
        $userId = Auth::id();
        $entrenadorId = DB::table('entrenadors')->where('user_id', $userId)->value('id');
        if (!$entrenadorId) {
            return [
                Stat::make('Atletas', 0)
                    ->descripction('No eres entrenador')
                    ->color('danger')
                    ->icon('heroicon-o-user-group'),
            ];
        }

        $totalAtletas = DB::table('atletas')->where('entrenador_id', $entrenadorId)->count();
        return [
            Stat::make('Atletas', $totalAtletas)
                ->description('Inscritos')
                ->color('primary')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
