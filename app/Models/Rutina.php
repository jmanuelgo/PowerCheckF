<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Atleta;
use App\Models\rutina_sets;

class Rutina extends Model
{
    //
    protected $table = 'rutinas';

    protected $fillable = [
        'entrenador_id',
        'atleta_id',
        'nombre',
        'objetivo',
        'dias_por_semana',
        'duracion_semanas',
        'version',
        'semana_actual',
        'dia_actual',
        'ultimo_dia_completado_at',
    ];

    // Relaciones
    public function entrenador()
    {
        return $this->belongsTo(User::class, 'entrenador_id');
    }

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }

    public function semanasRutina()
    {
        return $this->hasMany(SemanaRutina::class);
    }

    public function diasEntrenamiento()
    {
        return $this->hasManyThrough(DiaEntrenamiento::class, SemanaRutina::class);
    }

    // Método para crear la estructura automáticamente
    public function crearEstructura(): void
    {
        for ($semana = 1; $semana <= $this->duracion_semanas; $semana++) {
            $semanaRutina = $this->semanasRutina()->create([
                'numero_semana' => $semana
            ]);

            // Aquí podrías agregar lógica para crear los días según dias_por_semana
        }
    }

    public function sincronizarEstructura(array $data): void
    {
        // Eliminar toda la estructura existente
        $this->semanasRutina()->delete();

        // Crear nueva estructura
        if (!empty($data['semanas'])) {
            foreach ($data['semanas'] as $semanaData) {
                $semanaRutina = SemanaRutina::create([
                    'rutina_id' => $this->id,
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
    }

    public function sets()
    {
        return $this->hasMany(rutina_sets::class, 'rutina_id')
            ->orderBy('semana')
            ->orderBy('dia_semana')
            ->orderBy('orden');
    }

    // Es plantilla si no tiene atleta asignado
    public function getEsPlantillaAttribute(): bool
    {
        return $this->atleta_id === null;
    }

    // Avanzar al siguiente día
    public function avanzarDia(): void
    {
        $this->ultimo_dia_completado_at = now();

        if ($this->dia_actual < $this->dias_por_semana) {
            $this->dia_actual++;
        } else {
            $this->dia_actual = 1;
            $this->semana_actual++;
        }

        if ($this->semana_actual > $this->duracion_semanas) {
            // rutina terminada
            $this->semana_actual = $this->duracion_semanas;
            $this->dia_actual = $this->dias_por_semana;
        }

        $this->save();
    }
    public function dias()
    {
        return $this->hasMany(\App\Models\DiaEntrenamiento::class)
            ->orderByRaw("FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')");
    }
}
