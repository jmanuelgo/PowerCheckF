<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RutinaResource;
use App\Models\DiaEntrenamiento;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RutinasRecientesTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Última Rutinas Realizadas';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DiaEntrenamiento::query()
                    ->withMax('ejerciciosCompletados', 'fecha_completado')
                    ->with(['semanaRutina.rutina.atleta.user'])

                    ->whereHas('ejerciciosCompletados', function (Builder $query) {
                        $query->where('completado', true);
                    })

                    ->whereHas('semanaRutina.rutina', function (Builder $query) {
                        $query->where('entrenador_id', Auth::id());
                    })

                    ->orderByDesc('ejercicios_completados_max_fecha_completado')
            )
            ->columns([
                Tables\Columns\TextColumn::make('semanaRutina.rutina.atleta.user.name')
                    ->label('Atleta')
                    ->description(fn (DiaEntrenamiento $record) => $record->semanaRutina->rutina->atleta->user->apellidos ?? '')
                    ->searchable(),

                Tables\Columns\TextColumn::make('semanaRutina.rutina.nombre')
                    ->label('Rutina')
                    ->description(fn (DiaEntrenamiento $record) => 'Semana '.$record->semanaRutina->numero_semana.' - '.$record->dia_semana),

                Tables\Columns\TextColumn::make('ejercicios_completados_max_fecha_completado')
                    ->label('Fecha realizada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('progreso_calculado')
                    ->label('Efectividad')
                    ->state(function (DiaEntrenamiento $record) {

                        $ejercicios = $record->ejerciciosDia()
                            ->with(['series.seriesRealizadas'])
                            ->get();

                        $totalRepsObjetivo = 0;
                        $totalRepsRealizadas = 0;

                        foreach ($ejercicios as $ejercicioDia) {
                            foreach ($ejercicioDia->series as $seriePlanificada) {

                                $totalRepsObjetivo += $seriePlanificada->repeticiones_objetivo;
                                $totalRepsRealizadas += $seriePlanificada->seriesRealizadas->sum('repeticiones_realizadas');
                            }
                        }
                        if ($totalRepsObjetivo == 0) {
                            return '0%';
                        }

                        $porcentaje = ($totalRepsRealizadas / $totalRepsObjetivo) * 100;

                        return ($porcentaje > 100 ? '100' : round($porcentaje)).'%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int) $state >= 90 => 'success',
                        (int) $state >= 60 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->recordUrl(
                fn (DiaEntrenamiento $record): string => RutinaResource::getUrl('view', ['record' => $record->semanaRutina->rutina_id]));
    }
}
