<?php

namespace App\Filament\Resources\RutinaResource\Pages;

use App\Filament\Resources\RutinaResource;
use App\Models\Rutina;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRutina extends EditRecord
{
    protected static string $resource = RutinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar la estructura completa para edición
        $rutina = $this->getRecord();

        $data['semanas'] = $rutina->semanasRutina()
            ->with(['diasEntrenamiento.ejerciciosDia.seriesEjercicio', 'diasEntrenamiento.ejerciciosDia.ejercicio'])
            ->get()
            ->map(function ($semana) {
                return [
                    'numero_semana' => $semana->numero_semana,
                    'dias' => $semana->diasEntrenamiento->map(function ($dia) {
                        return [
                            'dia' => $dia->dia_semana,
                            'ejercicios' => $dia->ejerciciosDia->map(function ($ejercicioDia) {
                                return [
                                    'ejercicio_id' => $ejercicioDia->ejercicio_id,
                                    'orden' => $ejercicioDia->orden,
                                    'notas' => $ejercicioDia->notas,
                                    'series' => $ejercicioDia->seriesEjercicio->map(function ($serie) {
                                        return [
                                            'repeticiones' => $serie->repeticiones_objetivo,
                                            'peso' => $serie->peso_objetivo,
                                            'descanso' => $serie->descanso_segundos,
                                        ];
                                    })->toArray()
                                ];
                            })->toArray()
                        ];
                    })->toArray()
                ];
            })
            ->toArray();

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Rutina
    {
        // Actualizar datos básicos
        $record->update([
            'nombre' => $data['nombre'],
            'objetivo' => $data['objetivo'],
            'dias_por_semana' => $data['dias_por_semana'],
            'duracion_semanas' => $data['duracion_semanas'],
            'version' => $data['version'],
        ]);

        // Sincronizar toda la estructura (elimina lo viejo y crea lo nuevo)
        $record->sincronizarEstructura($data);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
