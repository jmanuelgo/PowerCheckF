<?php

namespace App\Filament\Widgets;

use App\Models\entrenador;
use App\Models\DiaEntrenamiento;
use App\Models\atleta;
use App\Models\User;
use App\Models\SemanaRutina;
use App\Models\Rutina;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RutinasRecientesTable extends BaseWidget
{
    protected static ?string $heading = 'Últimas Sesiones Completadas';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 6;


    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $entrenador = entrenador::where('user_id', Auth::id())->first();

                if (!$entrenador) {
                    return DiaEntrenamiento::query()->whereRaw('1 = 0'); // Query vacía
                }

                return DiaEntrenamiento::query()
                    ->whereHas('semanarutina.rutina.atleta', function (Builder $query) use ($entrenador) {
                        $query->where('entrenador_id', $entrenador->id);
                    })
                    ->whereNotNull('fecha_completado')
                    ->orderBy('fecha_completado', 'desc')
                    ->limit(5);
            })
            ->columns([
                TextColumn::make('atleta.user.name')
                    ->label('Atleta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('semana_rutina.rutina.nombre')
                    ->label('Rutina')
                    ->limit(30)
                    ->tooltip(fn($record): string => $record->semana_rutina?->rutina?->nombre ?? 'N/A'),
                TextColumn::make('dia_semana')
                    ->label('Día'),
                TextColumn::make('fecha_completado')
                    ->label('Fecha Completado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->emptyStateHeading('No hay sesiones completadas recientemente')
            ->paginated(false);
    }
}
