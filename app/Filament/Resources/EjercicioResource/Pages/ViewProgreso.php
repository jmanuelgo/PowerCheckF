<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use App\Filament\Resources\EjercicioResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Filament\Actions\Action;
use Illuminate\Support\Str;



class ViewProgreso extends ViewRecord
{
    protected static string $resource = EjercicioResource::class;

    protected static string $view = 'filament.resources.ejercicio-resource.pages.view-progreso';

    public array $stats = [];
    public array $historial = [];
    public array $pesoData = [];
    public array $repsData = [];

    public string $sortColumn = 'fecha';
    public string $sortDirection = 'desc';

    private function getBaseQuery()
    {
        $userId = Auth::id();
        $ejercicioId = $this->record->id;

        return DB::table('series_realizadas as srz')
            ->join('series_ejercicio as se', 'se.id', '=', 'srz.serie_ejercicio_id')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $ejercicioId)
            ->where('a.user_id', $userId)
            ->where('srz.completada', 1);
    }

    public function mount($record): void
    {
        parent::mount($record);
        abort_unless(Auth::user()?->hasRole('atleta'), 403, 'Acceso no autorizado.');

        $desde30dias = now()->subDays(30)->startOfDay();
        $baseQuery = $this->getBaseQuery();


        $datosStats = (clone $baseQuery)
            ->selectRaw('
                COUNT(srz.id) as total_sets_historico,
                MAX(CASE WHEN srz.fecha_realizacion >= ? THEN COALESCE(srz.peso_realizado, 0) * (1 + COALESCE(srz.repeticiones_realizadas, 0) / 30.0) ELSE 0 END) as rm_aprox_actual,
                MAX(COALESCE(srz.peso_realizado, 0) * (1 + COALESCE(srz.repeticiones_realizadas, 0) / 30.0)) as rm_mejor
            ', [$desde30dias])
            ->first();
        $setsRealizadas30d = (clone $baseQuery)->where('srz.fecha_realizacion', '>=', $desde30dias)->count();

        $setsPlaneadas30d = DB::table('series_ejercicio as se')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $this->record->id)
            ->where('a.user_id', Auth::id())
            ->whereBetween(DB::raw('DATE_ADD(r.created_at, INTERVAL (sw.numero_semana - 1) WEEK)'), [$desde30dias, now()])
            ->count();

        $adherencia = $setsPlaneadas30d > 0 ? round(($setsRealizadas30d / $setsPlaneadas30d) * 100) : null;

        $this->stats = [
            'rm_aprox_actual'   => round($datosStats->rm_aprox_actual ?? 0, 1),
            'rm_mejor'          => round($datosStats->rm_mejor ?? 0, 1),
            'total_sets'        => (int) ($datosStats->total_sets_historico ?? 0),
            'adherencia'        => $adherencia,
            'sets_planeadas'    => $setsPlaneadas30d,
            'sets_realizadas'   => $setsRealizadas30d,
        ];

        $progreso = (clone $baseQuery)
            ->selectRaw('DATE(srz.fecha_realizacion) as fecha, MAX(srz.peso_realizado) as max_peso, MAX(srz.repeticiones_realizadas) as max_reps')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $this->pesoData = [
            'labels' => $progreso->pluck('fecha')->toArray(),
            'data'   => $progreso->pluck('max_peso')->toArray(),
        ];
        $this->repsData = [
            'labels' => $progreso->pluck('fecha')->toArray(),
            'data'   => $progreso->pluck('max_reps')->toArray(),
        ];

        $this->updateHistorial();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'desc';
        }
        $this->updateHistorial();
    }

    public function updateHistorial(): void
    {
        $this->historial = $this->getBaseQuery()
            ->select(
                'srz.fecha_realizacion as fecha',
                'srz.peso_realizado as peso',
                'srz.repeticiones_realizadas as repeticiones'
            )
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->get()
            ->map(fn($r) => [
                'fecha'        => Carbon::parse($r->fecha)->translatedFormat('d M Y, H:i'),
                'peso'         => $r->peso,
                'repeticiones' => $r->repeticiones ?? 0,
            ])
            ->toArray();
    }


protected function getHeaderActions(): array
{
    return [
        Action::make('downloadPdf')
            ->label('Descargar Reporte PDF')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->action(function () {
                $ejercicio = $this->record;
                $stats = $this->stats;
                $historial = $this->historial;
                $progreso = $this->getBaseQuery()
                   ->selectRaw('DATE(srz.fecha_realizacion) as fecha, MAX(srz.peso_realizado) as max_peso, MAX(srz.repeticiones_realizadas) as max_reps')
                   ->groupBy('fecha')
                   ->orderBy('fecha', 'asc')
                   ->get()
                   ->map(function($item) {
                       $item->fecha = Carbon::parse($item->fecha);
                       return (array)$item;
                   })
                   ->toArray();

                $pdf = Pdf::loadView('pdf.progreso-report', compact(
                    'ejercicio',
                    'stats',
                    'historial',
                    'progreso'
                 ));
                 $filename = 'reporte-progreso-' . Str::slug($ejercicio->nombre) . '-' . now()->format('Ymd') . '.pdf';
                 return response()->streamDownload(function () use ($pdf) {
                     echo $pdf->output();
                 }, $filename);
            }),
    ];
}
}
