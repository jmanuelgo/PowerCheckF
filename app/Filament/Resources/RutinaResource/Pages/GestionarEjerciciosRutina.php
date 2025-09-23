<?php

namespace App\Filament\Resources\RutinaResource\Pages;

use App\Filament\Resources\RutinaResource;
use App\Models\Rutina;
use App\Models\SemanaRutina;
use App\Models\DiaEntrenamiento;
use App\Models\Ejercicio;
use App\Models\EjercicioDia;
use App\Models\SerieEjercicio;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;

class GestionarEjerciciosRutina extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = RutinaResource::class;
    protected static string $view = 'filament.resources.rutina-resource.pages.gestionar-ejercicios-rutina';

    public Rutina $record;
    public $semanas;
    public $ejerciciosData = [];

    public function mount(): void
    {
        $this->semanas = $this->record->semanasRutina()
            ->with(['diasEntrenamiento.ejerciciosDia.seriesEjercicio', 'diasEntrenamiento.ejerciciosDia.ejercicio'])
            ->get();
    }

    public function getTitle(): string|Htmlable
    {
        return "Gestionar Ejercicios: {$this->record->nombre}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Atleta: {$this->record->atleta->user->name} | {$this->record->dias_por_semana} días/semana × {$this->record->duracion_semanas} semanas";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('agregarEjercicio')
                ->label('Agregar Ejercicio')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Forms\Components\Select::make('semana_id')
                        ->label('Semana')
                        ->options($this->semanas->pluck('numero_semana', 'id'))
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('dia_id')
                        ->label('Día')
                        ->options(function (callable $get) {
                            $semanaId = $get('semana_id');
                            if (!$semanaId) return [];

                            $semana = $this->semanas->firstWhere('id', $semanaId);
                            return $semana ? $semana->diasEntrenamiento->pluck('dia_semana', 'id') : [];
                        })
                        ->required(),

                    Forms\Components\Select::make('ejercicio_id')
                        ->label('Ejercicio')
                        ->options(Ejercicio::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('orden')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),

                    Forms\Components\Textarea::make('notas')
                        ->label('Instrucciones')
                        ->rows(2)
                        ->placeholder('Técnica, precauciones, etc.'),

                    Repeater::make('series')
                        ->label('Series')
                        ->schema([
                            Forms\Components\TextInput::make('repeticiones_objetivo')
                                ->label('Repeticiones')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Forms\Components\TextInput::make('peso_objetivo')
                                ->label('Peso (kg)')
                                ->numeric()
                                ->step(0.5)
                                ->minValue(0),

                            Forms\Components\TextInput::make('descanso_segundos')
                                ->label('Descanso (seg)')
                                ->numeric()
                                ->minValue(0)
                                ->default(60),
                        ])
                        ->defaultItems(3)
                        ->minItems(1)
                        ->columns(3)
                ])
                ->action(function (array $data) {
                    $ejercicioDia = EjercicioDia::create([
                        'dia_entrenamiento_id' => $data['dia_id'],
                        'ejercicio_id' => $data['ejercicio_id'],
                        'orden' => $data['orden'],
                        'notas' => $data['notas']
                    ]);

                    foreach ($data['series'] as $index => $serie) {
                        SerieEjercicio::create([
                            'ejercicio_dia_id' => $ejercicioDia->id,
                            'numero_serie' => $index + 1,
                            'repeticiones_objetivo' => $serie['repeticiones_objetivo'],
                            'peso_objetivo' => $serie['peso_objetivo'] ?? null,
                            'descanso_segundos' => $serie['descanso_segundos'] ?? 60
                        ]);
                    }

                    Notification::make()
                        ->title('Ejercicio agregado correctamente')
                        ->success()
                        ->send();

                    $this->mount();
                }),
        ];
    }
}
