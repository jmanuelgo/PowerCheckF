<?php
// app/Filament/Widgets/RutinaPendienteDeHoyWidget.php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Rutina;
use App\Models\Atleta;

class RutinaPendienteDeHoyWidget extends Widget
{
    protected static string $view = 'filament.widgets.rutina-pendiente-de-hoy-widget';

    public ?Rutina $rutina = null;

    public ?int $semanaNum = null;
    public ?int $diaId = null;
    public ?string $diaLbl = null;

    public array $ejercicios = []; // para la vista

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasRole('atleta'), 403);

        $atletaId = Atleta::where('user_id', Auth::id())->value('id');

        $ordenDias = "FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";
        $this->rutina = Rutina::with([
            'atleta.user',
            'semanasRutina' => fn($q) => $q->orderBy('numero_semana'),
            'semanasRutina.diasEntrenamiento' => fn($q) => $q->orderByRaw($ordenDias),
            'semanasRutina.diasEntrenamiento.ejerciciosDia' => fn($q) => $q->orderBy('orden'),
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejercicio',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejerciciosCompletados',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio' => fn($q) => $q->orderBy('numero_serie'),
        ])->where('atleta_id', $atletaId)
            ->latest('id')
            ->first();

        if (! $this->rutina) {
            return;
        }

        // Encontrar el primer día PENDIENTE (no por calendario)
        [$sem, $diaId, $diaLbl] = $this->primerDiaPendiente($this->rutina);

        if ($sem === null) {
            // Todo completo: muestra el último día como referencia (opcional)
            $ultimaSemana = $this->rutina->semanasRutina->last();
            $ultimoDia = $ultimaSemana?->diasEntrenamiento?->last();
            $this->semanaNum = $ultimaSemana?->numero_semana;
            $this->diaId = $ultimoDia?->id;
            $this->diaLbl = $ultimoDia?->dia_semana;
            $this->ejercicios = $this->mapEjercicios($ultimoDia?->ejerciciosDia);
        } else {
            $this->semanaNum = $sem;
            $this->diaId = $diaId;
            $this->diaLbl = $diaLbl;

            $dia = $this->rutina->semanasRutina
                ->firstWhere('numero_semana', $sem)
                ?->diasEntrenamiento
                ?->firstWhere('id', $diaId);

            $this->ejercicios = $this->mapEjercicios($dia?->ejerciciosDia);
        }
    }

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('atleta');
    }

    /** Devuelve [numero_semana, dia_id, dia_label] o [null,null,null] si todo está completo */
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

    /** Un día se considera completado si
     * - tiene flag completado_por_atleta = 1 (si lo usas), o
     * - TODOS sus ejercicios tienen EjercicioCompletado->completado = 1
     */
    private function diaCompletado($dia): bool
    {
        if (property_exists($dia, 'completado_por_atleta') && (int)($dia->completado_por_atleta ?? 0) === 1) {
            return true;
        }

        $ejercicios = $dia?->ejerciciosDia ?? collect();
        if ($ejercicios->isEmpty()) {
            // decide si un día sin ejercicios es “completo” o “pendiente”
            return false;
        }

        foreach ($ejercicios as $ej) {
            $completado = $ej->ejerciciosCompletados->firstWhere('completado', true);
            if (! $completado) {
                return false;
            }
        }
        return true;
    }

    private function mapEjercicios($col)
    {
        if (!$col) return [];
        return $col->map(function ($ej) {
            return [
                'id' => $ej->id,
                'orden' => $ej->orden,
                'nombre' => $ej->ejercicio->nombre ?? '—',
                'series' => $ej->seriesEjercicio->map(fn($s) => [
                    'n' => $s->numero_serie,
                    'reps' => $s->repeticiones_objetivo,
                    'peso' => $s->peso_objetivo,
                    'rest' => $s->descanso_segundos,
                ])->values()->all(),
                'completo' => (bool) $ej->ejerciciosCompletados->firstWhere('completado', true),
            ];
        })->values()->all();
    }
}
