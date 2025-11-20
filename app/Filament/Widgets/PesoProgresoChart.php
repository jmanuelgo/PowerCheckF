<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ejercicio;

class PesoProgresoChart extends ChartWidget
{
    protected static ?string $type = 'line';
    protected static ?string $heading = 'Progresión de Peso Máximo (kg)';
    protected function getType(): string
    {
        return 'line';
    }


    public ?Ejercicio $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $progreso = DB::table('series_realizadas as srz')
            ->join('series_ejercicio as se', 'se.id', '=', 'srz.serie_ejercicio_id')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $this->record->id)
            ->where('a.user_id', Auth::id())
            ->where('srz.completada', 1)
            ->selectRaw('DATE(srz.fecha_realizacion) as fecha, MAX(srz.peso_realizado) as max_peso')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Peso Máximo (kg)',
                    'data' => $progreso->pluck('max_peso')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $progreso->pluck('fecha')->toArray(),
        ];
    }
}
