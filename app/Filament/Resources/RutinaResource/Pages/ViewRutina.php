<?php

namespace App\Filament\Resources\RutinaResource\Pages;

use App\Filament\Resources\RutinaResource;
use App\Models\Rutina;
use App\Models\EjercicioCompletado;
use App\Models\SerieRealizada;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewRutina extends ViewRecord
{
    protected static string $resource = RutinaResource::class;
    protected static string $view = 'filament.resources.rutina-resource.pages.view-rutina';

    public $repeticiones = [];
    public $peso = [];
    public $ejerciciosCompletados = [];

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->visible(fn() => auth()->user()->hasRole('entrenador')),
        ];
    }

    public function mount($record): void
    {
        parent::mount($record);
        $ordenDias = "FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";

        $this->record->load([
            'semanasRutina.diasEntrenamiento' => fn($q) => $q->orderByRaw($ordenDias),
            'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio.seriesRealizadas',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejerciciosCompletados',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejercicio',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio',
            'atleta.user',
            'entrenador'
        ]);

        $this->cargarEjerciciosCompletados();
    }

    protected function cargarEjerciciosCompletados()
    {
        $this->ejerciciosCompletados = [];

        foreach ($this->record->semanasRutina as $semana) {
            foreach ($semana->diasEntrenamiento as $dia) {
                foreach ($dia->ejerciciosDia as $ejercicioDia) {
                    $ejercicioCompletado = $this->getEjercicioCompletado($ejercicioDia->id);
                    $this->ejerciciosCompletados[$ejercicioDia->id] = $ejercicioCompletado ? $ejercicioCompletado->completado : false;
                }
            }
        }
    }

    public function toggleEjercicio($ejercicioDiaId)
    {
        if (!auth()->user()->hasRole('atleta')) {
            return;
        }

        $ejercicioCompletado = EjercicioCompletado::firstOrNew([
            'ejercicio_dia_id' => $ejercicioDiaId
        ]);

        $nuevoEstado = !$ejercicioCompletado->completado;
        $this->ejerciciosCompletados[$ejercicioDiaId] = $nuevoEstado;

        $ejercicioCompletado->completado = $nuevoEstado;
        $ejercicioCompletado->fecha_completado = now();
        $ejercicioCompletado->save();

        Notification::make()
            ->title($nuevoEstado ? 'Ejercicio marcado como completado' : 'Ejercicio desmarcado')
            ->success()
            ->send();
    }

    public function guardarSerie($serieId)
    {
        if (!auth()->user()->hasRole('atleta')) {
            return;
        }

        $repeticiones = $this->repeticiones[$serieId] ?? 0;
        $peso = $this->peso[$serieId] ?? 0;

        if ($repeticiones <= 0) {
            Notification::make()
                ->title('Debes ingresar las repeticiones realizadas')
                ->danger()
                ->send();
            return;
        }

        $serieEjercicio = \App\Models\SerieEjercicio::find($serieId);
        $ejercicioDiaId = $serieEjercicio->ejercicio_dia_id;

        $ejercicioCompletado = EjercicioCompletado::firstOrCreate([
            'ejercicio_dia_id' => $ejercicioDiaId
        ], [
            'completado' => true,
            'fecha_completado' => now()
        ]);

        SerieRealizada::create([
            'serie_ejercicio_id' => $serieId,
            'ejercicio_completado_id' => $ejercicioCompletado->id,
            'repeticiones_realizadas' => $repeticiones,
            'peso_realizado' => $peso,
            'completada' => true,
            'fecha_realizacion' => now()
        ]);

        unset($this->repeticiones[$serieId]);
        unset($this->peso[$serieId]);

        $this->dispatch('$refresh');

        Notification::make()
            ->title('Serie registrada correctamente')
            ->success()
            ->send();
    }

    public function editarSerie($serieId)
    {
        if (!auth()->user()->hasRole('atleta')) {
            return;
        }

        $serieRealizada = SerieRealizada::where('serie_ejercicio_id', $serieId)->first();

        if ($serieRealizada) {
            $this->repeticiones[$serieId] = $serieRealizada->repeticiones_realizadas;
            $this->peso[$serieId] = $serieRealizada->peso_realizado;

            $serieRealizada->delete();

            $this->record->load([
                'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio.seriesRealizadas'
            ]);
        }
    }

    public function getEjercicioCompletado($ejercicioDiaId)
    {
        return EjercicioCompletado::where('ejercicio_dia_id', $ejercicioDiaId)->first();
    }

    public function getSerieRealizada($serieId)
    {
        return SerieRealizada::where('serie_ejercicio_id', $serieId)->first();
    }

    public function getRutinaCompleta()
    {
        return $this->getRecord();
    }
}
