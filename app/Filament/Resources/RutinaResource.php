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
                                    $atleta = Atleta::with('user')->find($atletaId);
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
                            ->placeholder('Ej: 4'),

                        Forms\Components\TextInput::make('version')
                            ->default('1.0')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuración de Ejercicios')
                    ->description('Agrega semanas, días y ejercicios con sus series')
                    ->schema([
                        Forms\Components\Repeater::make('semanas')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('numero_semana')
                                    ->label('Semana')
                                    ->options(function ($get) {
                                        $duracion = $get('../../duracion_semanas') ?? 4;
                                        $options = [];
                                        for ($i = 1; $i <= $duracion; $i++) {
                                            $options[$i] = "Semana $i";
                                        }
                                        return $options;
                                    })
                                    ->required()
                                    ->default(1),

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
                                                    ->options(Ejercicio::all()->pluck('nombre', 'id'))
                                                    ->searchable()
                                                    ->required(),

                                                Forms\Components\TextInput::make('orden')
                                                    ->label('Orden')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\Textarea::make('notas')
                                                    ->label('Instrucciones')
                                                    ->rows(2)
                                                    ->placeholder('Técnica, precauciones, etc.'),

                                                Forms\Components\Repeater::make('series')
                                                    ->label('Series')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('repeticiones')
                                                            ->label('Repeticiones')
                                                            ->numeric()
                                                            ->minValue(1)
                                                            ->required()
                                                            ->default(12),

                                                        Forms\Components\TextInput::make('peso')
                                                            ->label('Peso (kg)')
                                                            ->numeric()
                                                            ->step(0.5)
                                                            ->minValue(0)
                                                            ->default(0),

                                                        Forms\Components\TextInput::make('descanso')
                                                            ->label('Descanso (seg)')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->default(60),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->minItems(0)
                                                    ->createItemButtonLabel('Agregar Serie')
                                                    ->columns(3),
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
                            ->defaultItems(0)
                            ->minItems(0)
                            ->createItemButtonLabel('Agregar Semana')
                            ->columns(1)
                            ->columnSpanFull(),
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
                    ->visible(fn () => auth()->user()->hasRole('entrenador')), // Solo visible para entrenadores

                Tables\Columns\TextColumn::make('entrenador.name')
                    ->label('Entrenador')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => auth()->user()->hasRole('atleta')), // Solo visible para atletas

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
                    ->visible(fn () => auth()->user()->hasRole('entrenador')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('entrenador')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('entrenador')),
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
}
