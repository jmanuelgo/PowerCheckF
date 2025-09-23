<?php

namespace App\Filament\Resources\RutinaResource\Pages;

use App\Filament\Resources\RutinaResource;
use App\Models\Rutina;
use App\Models\SemanaRutina;
use App\Models\DiaEntrenamiento;
use App\Models\EjercicioDia;
use App\Models\SerieEjercicio;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRutina extends CreateRecord
{
    protected static string $resource = RutinaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $rutina = Rutina::create([
            'entrenador_id' => $data['entrenador_id'],
            'atleta_id' => $data['atleta_id'],
            'nombre' => $data['nombre'],
            'objetivo' => $data['objetivo'],
            'dias_por_semana' => $data['dias_por_semana'],
            'duracion_semanas' => $data['duracion_semanas'],
            'version' => $data['version'],
        ]);

        if (!empty($data['semanas'])) {
            foreach ($data['semanas'] as $semanaData) {
                $semanaRutina = SemanaRutina::create([
                    'rutina_id' => $rutina->id,
                    'numero_semana' => $semanaData['numero_semana']
                ]);

                if (!empty($semanaData['dias'])) {
                    foreach ($semanaData['dias'] as $diaData) {
                        $diaEntrenamiento = DiaEntrenamiento::create([
                            'semana_rutina_id' => $semanaRutina->id,
                            'dia_semana' => $diaData['dia']
                        ]);

                        if (!empty($diaData['ejercicios'])) {
                            foreach ($diaData['ejercicios'] as $ejercicioData) {
                                $ejercicioDia = EjercicioDia::create([
                                    'dia_entrenamiento_id' => $diaEntrenamiento->id,
                                    'ejercicio_id' => $ejercicioData['ejercicio_id'],
                                    'orden' => $ejercicioData['orden'],
                                    'notas' => $ejercicioData['notas'] ?? null
                                ]);

                                if (!empty($ejercicioData['series'])) {
                                    foreach ($ejercicioData['series'] as $index => $serieData) {
                                        SerieEjercicio::create([
                                            'ejercicio_dia_id' => $ejercicioDia->id,
                                            'numero_serie' => $index + 1,
                                            'repeticiones_objetivo' => $serieData['repeticiones'],
                                            'peso_objetivo' => $serieData['peso'],
                                            'descanso_segundos' => $serieData['descanso']
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $rutina;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
