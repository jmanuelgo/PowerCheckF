<?php

// app/Filament/Widgets/RutinaPendienteDeHoyWidget.php

namespace App\Filament\Widgets;

use App\Models\Atleta;
use App\Models\EjercicioCompletado;
use App\Models\Rutina;
use App\Models\SerieEjercicio;
use App\Models\SerieRealizada;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class RutinaPendienteDeHoyWidget extends Widget
{
    protected static string $view = 'filament.widgets.rutina-pendiente-de-hoy-widget';

    protected int|string|array $columnSpan = 'full';

    public ?Rutina $rutina = null;

    public ?int $semanaNum = null;

    public ?int $diaId = null;

    public ?string $diaLbl = null;

    public array $ejercicios = [];

    // Propiedades para los formularios interactivos
    public array $repeticiones = [];

    public array $peso = [];

    public function mount(): void
    {
        $this->loadRoutineData();
    }

    #[On('rutina-actualizada')]
    public function loadRoutineData(): void
    {
        abort_unless(Auth::user()?->hasRole('atleta'), 403);
        $atletaId = Atleta::where('user_id', Auth::id())->value('id');
        $ordenDias = "FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";

        $this->rutina = Rutina::with([
            'semanasRutina' => fn ($q) => $q->orderBy('numero_semana'),
            'semanasRutina.diasEntrenamiento' => fn ($q) => $q->orderByRaw($ordenDias),
            'semanasRutina.diasEntrenamiento.ejerciciosDia' => fn ($q) => $q->orderBy('orden'),
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejercicio',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejerciciosCompletados.seriesRealizadas',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio' => fn ($q) => $q->orderBy('numero_serie'),
        ])->where('atleta_id', $atletaId)
            ->latest('id')
            ->first();

        if (! $this->rutina) {
            return;
        }

        [$sem, $diaId, $diaLbl] = $this->primerDiaPendiente($this->rutina);

        if ($sem === null) {
            $this->semanaNum = null;
            $this->diaId = null;
            $this->diaLbl = null;
            $this->ejercicios = [];
        } else {
            $this->semanaNum = $sem;
            $this->diaId = $diaId;
            $this->diaLbl = $diaLbl;
            $dia = $this->rutina->semanasRutina
                ->firstWhere('numero_semana', $sem)
                ?->diasEntrenamiento
                ?->firstWhere('id', $diaId);
            $this->ejercicios = $this->mapEjercicios($dia?->ejerciciosDia);
            $this->iniciarValores();
        }
    }

    private function primerDiaPendiente(Rutina $rutina): array
    {
        foreach ($rutina->semanasRutina as $semana) {
            foreach ($semana->diasEntrenamiento as $dia) {
                if (! $this->diaCompletado($dia)) {
                    return [$semana->numero_semana, $dia->id, $dia->dia_semana];
                }
            }
        }

        return [null, null, null];
    }

    private function diaCompletado($dia): bool
    {
        $ejercicios = $dia?->ejerciciosDia ?? collect();
        if ($ejercicios->isEmpty()) {
            return false;
        }
        foreach ($ejercicios as $ej) {
            if (! $ej->ejerciciosCompletados->contains('completado', true)) {
                return false;
            }
        }

        return true;
    }

    private function mapEjercicios($col)
    {
        if (! $col) {
            return [];
        }

        return $col->map(function ($ej) {
            return [
                'id' => $ej->id,
                'orden' => $ej->orden,
                'nombre' => $ej->ejercicio->nombre ?? '—',
                'descripcion' => $ej->ejercicio->descripcion,
                'series' => $ej->seriesEjercicio->map(fn ($s) => [
                    'id' => $s->id,
                    'n' => $s->numero_serie,
                    'reps' => $s->repeticiones_objetivo,
                    'peso' => $s->peso_objetivo,
                    'rest' => $s->descanso_segundos,
                ])->values()->all(),
                'completo' => $ej->ejerciciosCompletados->contains('completado', true),
            ];
        })->values()->all();
    }

    // --- MÉTODOS INTERACTIVOS AÑADIDOS ---

    public function getEjercicioCompletado($ejercicioDiaId)
    {
        return EjercicioCompletado::where('ejercicio_dia_id', $ejercicioDiaId)->first();
    }

    public function getSerieRealizada($serieId)
    {
        return SerieRealizada::where('serie_ejercicio_id', $serieId)->first();
    }

    public function toggleEjercicio($ejercicioDiaId)
    {
        $ejercicioCompletado = EjercicioCompletado::firstOrNew(['ejercicio_dia_id' => $ejercicioDiaId]);
        $nuevoEstado = ! $ejercicioCompletado->completado;
        $ejercicioCompletado->completado = $nuevoEstado;
        $ejercicioCompletado->fecha_completado = $nuevoEstado ? now() : null;
        $ejercicioCompletado->save();

        Notification::make()
            ->title($nuevoEstado ? 'Ejercicio marcado como completado' : 'Ejercicio desmarcado')
            ->success()
            ->send();

        $this->dispatch('rutina-actualizada');
    }

    public function guardarSerie($serieId)
    {
        $reps_input = $this->repeticiones[$serieId] ?? null;
        $peso_input = $this->peso[$serieId] ?? null;

        if (($reps_input !== null && $reps_input !== '') && ! is_numeric($reps_input)) {
            Notification::make()->title('Las repeticiones deben ser un número.')->danger()->send();

            return;
        }

        if (($peso_input !== null && $peso_input !== '') && ! is_numeric($peso_input)) {
            Notification::make()->title('El peso debe ser un número.')->danger()->send();

            return;
        }

        $repeticiones = (int) $reps_input;
        $peso = (float) $peso_input;

        $serieEjercicio = SerieEjercicio::find($serieId);
        $ejercicioDiaId = $serieEjercicio->ejercicio_dia_id;

        $ejercicioCompletado = EjercicioCompletado::firstOrCreate(
            ['ejercicio_dia_id' => $ejercicioDiaId]
        );

        SerieRealizada::updateOrCreate(
            ['serie_ejercicio_id' => $serieId, 'ejercicio_completado_id' => $ejercicioCompletado->id],
            [
                'repeticiones_realizadas' => $repeticiones,
                'peso_realizado' => $peso,
                'completada' => true,
                'fecha_realizacion' => now(),
            ]
        );

        unset($this->repeticiones[$serieId], $this->peso[$serieId]);
        $this->dispatch('rutina-actualizada');

        Notification::make()->title('Serie registrada correctamente')->success()->send();
    }

    public function editarSerie($serieId)
    {
        $serieRealizada = SerieRealizada::where('serie_ejercicio_id', $serieId)->first();

        if ($serieRealizada) {
            $this->repeticiones[$serieId] = $serieRealizada->repeticiones_realizadas;
            $this->peso[$serieId] = $serieRealizada->peso_realizado;
            $serieRealizada->delete();
            $this->dispatch('rutina-actualizada');
        }
    }

    private function iniciarValores(): void
    {
        foreach ($this->ejercicios as $ej) {
            foreach ($ej['series'] as $s) {
                $serieId = $s['id'];
                if (! $this->getSerieRealizada($serieId)) {
                    if (! isset($this->repeticiones[$serieId])) {
                        $this->repeticiones[$serieId] = $s['reps'];
                    }
                    if (! isset($this->peso[$serieId])) {
                        $this->peso[$serieId] = $s['peso'];
                    }
                }
            }
        }
    }
}
