<?php
// app/Filament/Pages/MiRutinaDeHoy.php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Rutina;
use App\Models\Atleta;
use Carbon\Carbon;
use App\Support\DiaSemana;

class MiRutinaDeHoy extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Mi rutina de hoy';
    protected static string $view = 'filament.pages.mi-rutina-de-hoy';

    public $rutina;
    public $semanaActiva;
    public $diaDeHoy;   // label (“Lunes”, “Martes”, …)
    public $diaId;      // id del dia_entrenamiento seleccionado (para ser 100% exactos)

    public function mount()
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('atleta'), 403);

        $atletaId = Atleta::where('user_id', $user->id)->value('id');

        // Trae TODO en orden correcto
        $ordenDias = "FIELD(dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";
        $this->rutina = Rutina::with([
            'atleta.user',
            'semanasRutina' => fn($q) => $q->orderBy('numero_semana'),
            'semanasRutina.diasEntrenamiento' => fn($q) => $q->orderByRaw($ordenDias),
            'semanasRutina.diasEntrenamiento.ejerciciosDia' => fn($q) => $q->orderBy('orden'),
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejercicio',
            'semanasRutina.diasEntrenamiento.ejerciciosDia.seriesEjercicio' => fn($q) => $q->orderBy('numero_serie'),
            'semanasRutina.diasEntrenamiento.ejerciciosDia.ejerciciosCompletados',
        ])->where('atleta_id', $atletaId)
            ->latest('id')
            ->first();

        if (! $this->rutina) {
            return;
        }

        // Elegir el primer día PENDIENTE (no por calendario)
        [$semanaNum, $diaId, $diaLabel] = $this->buscarPrimerDiaPendiente($this->rutina);

        if ($semanaNum === null) {
            // Todo completado
            $ultimaSemana = $this->rutina->semanasRutina->last();
            $ultimoDia = $ultimaSemana?->diasEntrenamiento?->last();
            $this->semanaActiva = $ultimaSemana?->numero_semana ?? 1;
            $this->diaId = $ultimoDia?->id;
            $this->diaDeHoy = $ultimoDia?->dia_semana ?? 'Lunes';
        } else {
            $this->semanaActiva = $semanaNum;
            $this->diaId = $diaId;
            $this->diaDeHoy = $diaLabel;
        }
    }

    /**
     * Devuelve [numero_semana, dia_id, dia_label] del primer día pendiente,
     * o [null, null, null] si todo está completado.
     */
    private function buscarPrimerDiaPendiente(Rutina $rutina): array
    {
        foreach ($rutina->semanasRutina->sortBy('numero_semana') as $semana) {
            foreach ($semana->diasEntrenamiento as $dia) {
                if (! $this->estaDiaCompletado($dia)) {
                    return [$semana->numero_semana, $dia->id, $dia->dia_semana];
                }
            }
        }
        return [null, null, null];
    }

    /**
     * Un día se considera completado si:
     * - completado_por_atleta = 1, o
     * - todos los ejercicios del día tienen un EjercicioCompletado->completado = 1
     */
    private function estaDiaCompletado($dia): bool
    {
        // Si usas el flag en la tabla de días:
        if (!is_null($dia->completado_por_atleta) && (int) $dia->completado_por_atleta === 1) {
            return true;
        }

        $ejercicios = $dia->ejerciciosDia ?? collect();
        if ($ejercicios->isEmpty()) {
            // Si decides que un día sin ejercicios cuenta como completo, devuélvelo true.
            return false;
        }

        // Todos los ejercicios deben estar marcados como completados
        foreach ($ejercicios as $ej) {
            $completado = $ej->ejerciciosCompletados
                ->firstWhere('completado', true);
            if (!$completado) {
                return false;
            }
        }
        return true;
    }
}
