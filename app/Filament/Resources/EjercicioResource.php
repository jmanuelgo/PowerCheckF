<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EjercicioResource\Pages;
use App\Models\Ejercicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'Fuerza'       => 'Fuerza',
                        'Cardio'       => 'Cardio',
                        'Flexibilidad' => 'Flexibilidad',
                        'Potencia'     => 'Potencia',
                    ])
                    ->default('Fuerza')
                    ->required(),

                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {

        $canCreate     = \Illuminate\Support\Facades\Gate::allows('create_ejercicio');
        $canUpdate     = \Illuminate\Support\Facades\Gate::allows('update_ejercicio');
        $canDelete     = \Illuminate\Support\Facades\Gate::allows('delete_ejercicio');
        $canDeleteAny  = \Illuminate\Support\Facades\Gate::allows('delete_any_ejercicio');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'Fuerza'       => 'Fuerza',
                        'Cardio'       => 'Cardio',
                        'Flexibilidad' => 'Flexibilidad',
                        'Potencia'     => 'Potencia',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), 
                Tables\Actions\Action::make('progreso')
                    ->label('Progreso')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn($record) => static::getUrl('progreso', ['record' => $record]))
                    ->visible(fn() => auth()->user()?->hasRole('atleta')),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => $canUpdate),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => $canDelete),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $canDeleteAny || $canDelete),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn() => $canCreate),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user?->hasRole('atleta')) {
            $query->whereIn('ejercicios.id', function ($q) use ($user) {
                $q->select('ed.ejercicio_id')
                    ->from('ejercicios_dia as ed')
                    ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
                    ->join('semanas_rutina as sr', 'sr.id', '=', 'de.semana_rutina_id')
                    ->join('rutinas as r', 'r.id', '=', 'sr.rutina_id')
                    ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
                    ->where('a.user_id', $user->id);
            });
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEjercicios::route('/'),
            'create' => Pages\CreateEjercicio::route('/create'),
            'edit'   => Pages\EditEjercicio::route('/{record}/edit'),
            'progreso'  => Pages\ViewProgreso::route('/{record}/progreso'),
        ];
    }
}
