<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutinaResource\Pages;
use App\Models\Rutina;
use App\Models\Atleta;
use App\Models\Ejercicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Group;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;

class RutinaResource extends Resource
{
    protected static ?string $model = Rutina::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\Hidden::make('entrenador_id')
                            ->default(auth()->id()),

                        Forms\Components\Hidden::make('atleta_id')
                            ->default(function () {
                                $atletaId = request()->get('atleta_id');
                                return $atletaId ?: null;
                            })
                            ->required(),

                        Forms\Components\Placeholder::make('atleta_info')
                            ->label('Atleta asignado')
                            ->content(function ($get) {
                                $atletaId = $get('atleta_id') ?? request()->get('atleta_id');
                                if ($atletaId) {
                                    $atleta = \App\Models\Atleta::with('user')->find($atletaId);
                                    return $atleta ? $atleta->user->name : 'Atleta no encontrado';
                                }
                                return 'No se ha seleccionado atleta';
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Rutina Fuerza Inicial'),

                        Forms\Components\TextInput::make('objetivo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Ganar masa muscular'),

                        Forms\Components\TextInput::make('dias_por_semana')
                            ->label('Días por semana')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(7)
                            ->required()
                            ->default(null)
                            ->placeholder('Ej: 3'),

                        Forms\Components\TextInput::make('duracion_semanas')
                            ->label('Duración en semanas')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(52)
                            ->required()
                            ->default(null)
                            ->placeholder('Ej: 4')
                            ->reactive()
                            ->afterStateHydrated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $dur = (int) ($state ?: 0);
                                $semanas = $get('semanas') ?? [];
                                if ($dur <= 0 || ! empty($semanas)) {
                                    return;
                                }
                                $nuevo = [];
                                for ($i = 1; $i <= $dur; $i++) {
                                    $nuevo[] = ['numero_semana' => $i, 'dias' => []];
                                }
                                $set('semanas', $nuevo);
                                $act = (int) ($get('semana_activa') ?? 1);
                                if ($act < 1 || $act > $dur) {
                                    $set('semana_activa', 1);
                                }
                            })
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $dur = (int) ($state ?: 0);
                                if ($dur <= 0) {
                                    return;
                                }
                                $semanas = $get('semanas') ?? [];
                                $actual = count($semanas);
                                $nuevo = [];
                                for ($i = 1; $i <= $dur; $i++) {
                                    $existente = $semanas[$i - 1] ?? null;
                                    $nuevo[] = [
                                        'numero_semana' => $i,
                                        'dias' => $existente['dias'] ?? [],
                                    ];
                                }

                                $set('semanas', $nuevo);
                                $act = (int) ($get('semana_activa') ?? 1);
                                if ($act < 1 || $act > $dur) {
                                    $set('semana_activa', min(max(1, $act), $dur));
                                }
                            }),

                        Forms\Components\TextInput::make('version')
                            ->default('1.0')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Select::make('semana_activa')
                    ->label('Semana a editar')
                    ->helperText('Selecciona qué semana quieres ver/editar.')
                    ->options(function (Forms\Get $get) {
                        $count = count($get('semanas') ?? []);
                        $opts = [];
                        for ($i = 1; $i <= $count; $i++) {
                            $opts[$i] = "Semana $i";
                        }
                        return $opts;
                    })
                    ->default(1)
                    ->reactive()
                    ->dehydrated(false),

                Forms\Components\Section::make('Configuración de Ejercicios')
                    ->description('Agrega semanas, días y ejercicios con sus series')
                    ->headerActions([
                        Action::make('clonarSemanaActiva')
                            ->label('Clonar semana')
                            ->icon('heroicon-m-document-duplicate')
                            ->modalHeading('Clonar semana activa')
                            ->modalDescription('Se duplicará la semana seleccionada y se creará como la siguiente semana. Puedes subir pesos en kg o porcentaje.')
                            ->form([
                                ToggleButtons::make('tipo')
                                    ->label('Tipo de incremento')
                                    ->options([
                                        'kg'         => 'Kg',
                                        'porcentaje' => '%',
                                    ])
                                    ->inline()
                                    ->default('kg')
                                    ->required(),
                                Forms\Components\TextInput::make('valor')
                                    ->label('Valor de incremento')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.5)
                                    ->default(2.5)
                                    ->required()
                                    ->helperText('Ej.: 2.5 kg o 5 % según el tipo.'),
                            ])
                            ->action(function (array $data, Get $get, Set $set) {
                                $semanas = $get('semanas') ?? [];
                                if (empty($semanas)) {
                                    Notification::make()->title('No hay semanas para clonar')->danger()->send();
                                    return;
                                }

                                $activa = (int) ($get('semana_activa') ?? 1);
                                $origen = null;
                                foreach ($semanas as $s) {
                                    if ((int) ($s['numero_semana'] ?? 0) === $activa) {
                                        $origen = $s;
                                        break;
                                    }
                                }
                                if (!$origen) {
                                    Notification::make()->title("No se encontró la Semana {$activa}")->danger()->send();
                                    return;
                                }
                                $maxNum = 0;
                                foreach ($semanas as $s) {
                                    $maxNum = max($maxNum, (int) ($s['numero_semana'] ?? 0));
                                }
                                $nuevoNumero = max($activa + 1, $maxNum + 1);

                                $tipo  = $data['tipo']  ?? 'kg';
                                $valor = (float) ($data['valor'] ?? 0);
                                $clon = $origen;
                                $clon['numero_semana'] = $nuevoNumero;

                                if (!isset($clon['dias']) || !is_array($clon['dias'])) {
                                    $clon['dias'] = [];
                                }

                                foreach ($clon['dias'] as &$dia) {
                                    if (!isset($dia['ejercicios']) || !is_array($dia['ejercicios'])) {
                                        $dia['ejercicios'] = [];
                                    }
                                    $orden = 1;
                                    foreach ($dia['ejercicios'] as &$ej) {
                                        $ej['orden'] = $orden++;

                                        if (!isset($ej['series']) || !is_array($ej['series'])) {
                                            $ej['series'] = [];
                                        }

                                        foreach ($ej['series'] as &$serie) {
                                            $peso = (float) ($serie['peso'] ?? 0);
                                            if ($tipo === 'porcentaje') {
                                                $peso = $peso * (1 + ($valor / 100));
                                            } else {
                                                $peso = $peso + $valor;
                                            }
                                            $serie['peso'] = round($peso, 2);
                                        }
                                        unset($serie);
                                    }
                                    unset($ej);
                                }
                                unset($dia);
                                $semanas[] = $clon;
                                usort($semanas, fn($a, $b) => ((int) ($a['numero_semana'] ?? 0)) <=> ((int) ($b['numero_semana'] ?? 0)));
                                $durActual = (int) ($get('duracion_semanas') ?? 0);
                                if ($nuevoNumero > $durActual) {
                                    $set('duracion_semanas', $nuevoNumero);
                                }
                                $set('semanas', array_values($semanas));
                                $set('semana_activa', $nuevoNumero);

                                Notification::make()
                                    ->title('Semana clonada')
                                    ->body("Se creó la Semana {$nuevoNumero} a partir de la Semana {$activa}.")
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        Forms\Components\Repeater::make('semanas')
                            ->label('Semanas')
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['numero_semana']) ? "Semana {$state['numero_semana']}" : null
                            )
                            ->reactive()
                            ->schema([
                                Forms\Components\Hidden::make('numero_semana'),
                                Section::make('Contenido de la semana')
                                    ->schema([
                                        Forms\Components\Repeater::make('dias')
                                            ->label('Días')
                                            ->schema([
                                                Forms\Components\Select::make('dia')
                                                    ->label('Día')
                                                    ->options([
                                                        'Lunes' => 'Lunes',
                                                        'Martes' => 'Martes',
                                                        'Miércoles' => 'Miércoles',
                                                        'Jueves' => 'Jueves',
                                                        'Viernes' => 'Viernes',
                                                        'Sábado' => 'Sábado',
                                                        'Domingo' => 'Domingo',
                                                    ])
                                                    ->required()
                                                    ->default('Lunes'),
                                                Forms\Components\Repeater::make('ejercicios')
                                                    ->label('Ejercicios')
                                                    ->schema([
                                                        Forms\Components\Select::make('ejercicio_id')
                                                            ->label('Ejercicio')
                                                            ->options(\App\Models\Ejercicio::all()->pluck('nombre', 'id'))
                                                            ->searchable()
                                                            ->required(),

                                                        Forms\Components\Textarea::make('notas')
                                                            ->label('Instrucciones')
                                                            ->rows(2)
                                                            ->placeholder('Técnica, precauciones, etc.'),

                                                        Forms\Components\Section::make('Series')
                                                            ->headerActions([
                                                                \Filament\Forms\Components\Actions\Action::make('addOneSeries')
                                                                    ->label('Agregar 1 serie')
                                                                    ->action(function (Get $get, Set $set) {
                                                                        $series = $get('series') ?? [];
                                                                        $series[] = ['repeticiones' => 12, 'peso' => 0, 'descanso' => 60];
                                                                        $set('series', $series);
                                                                    }),

                                                                \Filament\Forms\Components\Actions\Action::make('presetSeries')
                                                                    ->label('Agregar series ')
                                                                    ->modalHeading('Agregar series ')
                                                                    ->modalDescription('Crea varias series iguales de una sola vez.')
                                                                    ->form([
                                                                        Forms\Components\TextInput::make('cantidad')->label('Cantidad de series')->numeric()->minValue(1)->default(3)->required(),
                                                                        Forms\Components\TextInput::make('repeticiones')->label('Repeticiones')->numeric()->minValue(1)->default(8)->required(),
                                                                        Forms\Components\TextInput::make('peso')->label('Peso (kg)')->numeric()->minValue(0)->step(0.5)->default(10)->required(),
                                                                        Forms\Components\TextInput::make('descanso')->label('Descanso (seg)')->numeric()->minValue(0)->default(60)->required(),
                                                                        \Filament\Forms\Components\ToggleButtons::make('modo')
                                                                            ->label('Modo')->options([
                                                                                'append'  => 'Agregar al final',
                                                                                'replace' => 'Reemplazar existentes',
                                                                            ])->inline()->default('append')->required(),
                                                                    ])
                                                                    ->action(function (array $data, Get $get, Set $set) {
                                                                        $seriesActuales = $get('series') ?? [];
                                                                        $n    = (int) ($data['cantidad'] ?? 0);
                                                                        $rep  = (int) ($data['repeticiones'] ?? 0);
                                                                        $kg   = (float) ($data['peso'] ?? 0);
                                                                        $rest = (int) ($data['descanso'] ?? 0);
                                                                        $modo = $data['modo'] ?? 'append';
                                                                        if ($n <= 0 || $rep <= 0) return;

                                                                        $nuevas = [];
                                                                        for ($i = 0; $i < $n; $i++) {
                                                                            $nuevas[] = ['repeticiones' => $rep, 'peso' => $kg, 'descanso' => $rest];
                                                                        }

                                                                        $set('series', $modo === 'replace' ? $nuevas : array_merge($seriesActuales, $nuevas));
                                                                    }),
                                                            ])
                                                            ->schema([
                                                                Forms\Components\Repeater::make('series')
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('repeticiones')->label('Repeticiones')->numeric()->minValue(1)->required()->default(12),
                                                                        Forms\Components\TextInput::make('peso')->label('Peso (kg)')->numeric()->step(0.5)->minValue(0)->default(0),
                                                                        Forms\Components\TextInput::make('descanso')->label('Descanso (seg)')->numeric()->minValue(0)->default(60),
                                                                    ])
                                                                    ->addable(false)
                                                                    ->defaultItems(0)
                                                                    ->minItems(0)
                                                                    ->columns(3),
                                                            ]),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->minItems(0)
                                                    ->createItemButtonLabel('Agregar Ejercicio')
                                                    ->columns(1),
                                            ])
                                            ->defaultItems(0)
                                            ->minItems(0)
                                            ->createItemButtonLabel('Agregar Día')
                                            ->columns(1),
                                    ])
                                    ->collapsible()
                                    ->collapsed(function (\Filament\Forms\Get $get, ?array $state) {
                                        $activa = (int) ($get('../../semana_activa') ?? 1);
                                        $n = (int) ($state['numero_semana'] ?? 0);
                                        return $n !== $activa;
                                    }),
                            ])
                            ->defaultItems(0)
                            ->minItems(0)
                            ->createItemButtonLabel('Agregar Semana')
                            ->columns(1)
                            ->columnSpanFull()


                    ]),


                Forms\Components\Section::make('Notas Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('notas_generales')
                            ->label('Notas generales')
                            ->rows(3)
                            ->placeholder('Instrucciones generales, objetivos, etc.'),
                    ])
                    ->collapsible(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('atleta.user.name')
                    ->label('Atleta')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasRole('entrenador')), 

                Tables\Columns\TextColumn::make('entrenador.name')
                    ->label('Entrenador')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasRole('atleta')), 

                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('objetivo')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('duracion_semanas')
                    ->label('Semanas')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('dias_por_semana')
                    ->label('Días/Sem')
                    ->numeric()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('atleta_id')
                    ->label('Atleta')
                    ->options(function () {
                        $entrenadorId = auth()->id();
                        return Atleta::where('entrenador_id', $entrenadorId)
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id');
                    })
                    ->visible(fn() => auth()->user()->hasRole('entrenador')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole('entrenador')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('entrenador')),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['atleta.user', 'entrenador']);

        if (auth()->user()->hasRole('entrenador')) {
            return $query->where('entrenador_id', auth()->id());
        } elseif (auth()->user()->hasRole('atleta')) {
            $atletaId = Atleta::where('user_id', auth()->id())->value('id');
            return $query->where('atleta_id', $atletaId);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('entrenador');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('entrenador');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('entrenador');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRutinas::route('/'),
            'create' => Pages\CreateRutina::route('/create'),
            'edit' => Pages\EditRutina::route('/{record}/edit'),
            'view' => Pages\ViewRutina::route('/{record}'),
        ];
    }
    protected static function normalizarSemanas(array $semanas): array
    {
        $semanas = array_values($semanas); 
        $resultado = [];
        $n = 1;

        foreach ($semanas as $semana) {
            $semana['numero_semana'] = $n++;
            $semana['dias'] = array_values($semana['dias'] ?? []);

            foreach ($semana['dias'] as &$dia) {
                $dia['ejercicios'] = array_values($dia['ejercicios'] ?? []);
                $orden = 1;
                foreach ($dia['ejercicios'] as &$ej) {
                    $ej['orden'] = $orden++;
                    $ej['series'] = array_values($ej['series'] ?? []);
                }
                unset($ej);
            }
            unset($dia);

            $resultado[] = $semana;
        }

        return $resultado;
    }
}
