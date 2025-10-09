<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use App\Filament\Resources\EjercicioResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewProgreso extends ViewRecord
{
    protected static string $resource = EjercicioResource::class;

    // Blade que renderiza la vista
    protected static string $view = 'filament.resources.ejercicio-resource.pages.view-progreso';

    // Datos que mostraremos en la vista
    public array $stats = [];
    public array $historial = [];

    public function mount($record): void
    {
        parent::mount($record);

        // 🔒 Solo atletas
        abort_unless(Auth::user()?->hasRole('atleta'), 403);

        $userId      = Auth::id();
        $ejercicioId = $this->record->id;
        $desde       = now()->subDays(30);

        // --- Planeado (de la rutina) ---
        $planeado = DB::table('series_ejercicio as se')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $ejercicioId)
            ->where('a.user_id', $userId)
            ->selectRaw('COUNT(*) as sets_planeadas')
            ->selectRaw('SUM(CASE WHEN se.peso_objetivo>0 THEN se.peso_objetivo*se.repeticiones_objetivo ELSE 0 END) as volumen_planeado')
            ->first();

        // --- Realizado (últimos 30 días) ---
        $realizado = DB::table('series_realizadas as srz')
            ->join('ejercicios_completados as ec', 'ec.id', '=', 'srz.ejercicio_completado_id')
            ->join('series_ejercicio as se', 'se.id', '=', 'srz.serie_ejercicio_id')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $ejercicioId)
            ->where('a.user_id', $userId)
            ->where('srz.fecha_realizacion', '>=', $desde)
            ->selectRaw('COUNT(*) as sets_realizadas')
            ->selectRaw('SUM(COALESCE(srz.peso_realizado,0)*COALESCE(srz.repeticiones_realizadas,0)) as volumen_realizado')
            ->selectRaw('MAX(COALESCE(srz.peso_realizado,0) * (1 + COALESCE(srz.repeticiones_realizadas,0)/30.0)) as rm_aprox_actual')
            ->first();

        // --- Mejor RM histórico ---
        $rmMejor = DB::table('series_realizadas as srz')
            ->join('ejercicios_completados as ec', 'ec.id', '=', 'srz.ejercicio_completado_id')
            ->join('series_ejercicio as se', 'se.id', '=', 'srz.serie_ejercicio_id')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $ejercicioId)
            ->where('a.user_id', $userId)
            ->selectRaw('MAX(COALESCE(srz.peso_realizado,0) * (1 + COALESCE(srz.repeticiones_realizadas,0)/30.0)) as rm_mejor')
            ->value('rm_mejor');

        $setsPlaneadas   = (int) ($planeado->sets_planeadas ?? 0);
        $setsRealizadas  = (int) ($realizado->sets_realizadas ?? 0);
        $adherencia      = $setsPlaneadas > 0 ? round($setsRealizadas / $setsPlaneadas * 100) : null;

        $this->stats = [
            'rm_aprox_actual'  => round($realizado->rm_aprox_actual ?? 0, 1),
            'rm_mejor'         => round($rmMejor ?? 0, 1),
            'volumen_planeado' => (float) ($planeado->volumen_planeado ?? 0),
            'volumen_realizado' => (float) ($realizado->volumen_realizado ?? 0),
            'adherencia'       => $adherencia,
            'sets_planeadas'   => $setsPlaneadas,
            'sets_realizadas'  => $setsRealizadas,
            'desde'            => $desde->toDateString(),
        ];

        // --- Historial simple por día (RM aprox. máximo del día) ---
        $this->historial = DB::table('series_realizadas as srz')
            ->join('ejercicios_completados as ec', 'ec.id', '=', 'srz.ejercicio_completado_id')
            ->join('series_ejercicio as se', 'se.id', '=', 'srz.serie_ejercicio_id')
            ->join('ejercicios_dia as ed', 'ed.id', '=', 'se.ejercicio_dia_id')
            ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
            ->join('semanas_rutina as sw', 'sw.id', '=', 'de.semana_rutina_id')
            ->join('rutinas as r', 'r.id', '=', 'sw.rutina_id')
            ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
            ->where('ed.ejercicio_id', $ejercicioId)
            ->where('a.user_id', $userId)
            ->where('srz.fecha_realizacion', '>=', $desde)
            ->selectRaw('DATE(srz.fecha_realizacion) as fecha')
            ->selectRaw('MAX(COALESCE(srz.peso_realizado,0) * (1 + COALESCE(srz.repeticiones_realizadas,0)/30.0)) as rm')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(fn($r) => ['fecha' => $r->fecha, 'rm' => round($r->rm ?? 0, 1)])
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return []; // sin acciones (solo vista)
    }
}
