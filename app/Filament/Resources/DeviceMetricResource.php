<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceMetricResource\Pages;
use App\Models\DeviceMetric;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 

class DeviceMetricResource extends Resource
{
    protected static ?string $model = DeviceMetric::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Métricas';
    protected static ?string $pluralModelLabel = 'Métricas de Dispositivo';
    protected static ?int $navigationSort = 99; 
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('athlete_id')->relationship('athlete', 'name')->disabled(),
                Forms\Components\Select::make('device_id')->relationship('device', 'name')->disabled(),
                Forms\Components\DateTimePicker::make('captured_at')->disabled(),
                Forms\Components\TextInput::make('ejercicio')->disabled(),
                Forms\Components\TextInput::make('repeticiones')->disabled(),
                Forms\Components\TextInput::make('bpm')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $canViewAny = Gate::allows('view_any_device::metric');

        return $table
            ->columns([
                TextColumn::make('captured_at')
                    ->label('Fecha')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('athlete.name')
                    ->label('Atleta')
                    ->visible($canViewAny)
                    ->searchable(),

                TextColumn::make('device.name')
                    ->label('Dispositivo')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ejercicio')
                    ->label('Ejercicio')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('repeticiones')
                    ->label('Reps')
                    ->sortable(),

                TextColumn::make('bpm')
                    ->label('BPM')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Desde'),
                        Forms\Components\DatePicker::make('to')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['from']) {
                            $query->whereDate('captured_at', '>=', $data['from']);
                        }
                        if ($data['to']) {
                            $query->whereDate('captured_at', '<=', $data['to']);
                        }
                    }),

                SelectFilter::make('athlete_id')
                    ->label('Atleta')
                    ->options(fn () => User::query()
                        ->whereHas('roles', fn ($q) => $q->where('name', 'atleta'))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->visible($canViewAny),

                SelectFilter::make('ejercicio')
                    ->label('Ejercicio')
                    ->options(fn () => DeviceMetric::query()
                        ->whereNotNull('ejercicio')
                        ->distinct()
                        ->orderBy('ejercicio')
                        ->pluck('ejercicio', 'ejercicio')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('captured_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceMetrics::route('/'),
            'view' => Pages\EditDeviceMetric::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();
        if ($user->hasRole('atleta')) {
            return $query->where('athlete_id', $user->id);
        }
        return $query;
    }
}