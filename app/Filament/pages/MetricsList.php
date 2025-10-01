<?php

namespace App\Filament\Pages;

use App\Models\DeviceMetric;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class MetricsList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Mis métricas';
    protected static ?string $title           = 'Datos del dispositivo';
    protected static ?string $slug            = 'metrics';
    protected static string  $view            = 'filament.pages.metrics-list';

    // Mostrar en menú solo si tiene permiso (Shield genera page_MetricsList)
    public static function shouldRegisterNavigation(): bool
    {
        $u = Filament::auth()->user();
        return $u ? Gate::forUser($u)->allows('page_MetricsList') : false;
    }

    // Bloquear acceso directo si no tiene permiso
    public static function canAccess(): bool
    {
        $u = Filament::auth()->user();
        return $u ? Gate::forUser($u)->allows('page_MetricsList') : false;
    }

public function table(Table $table): Table
{
    $user = Filament::auth()->user();

    $query = DeviceMetric::query()
        ->with(['device','athlete'])
        ->latest('id');

    $canViewAny = $user && Gate::forUser($user)->allows('metrics.view_any');

    if (! $canViewAny) {
        $query->where('athlete_id', (int) ($user?->id ?? 0));
    }

    return $table
        ->query($query)
        ->columns([
            TextColumn::make('created_at')
                ->label('Fecha')
                ->formatStateUsing(fn ($state, DeviceMetric $record) => $record->captured_at ?? $record->created_at)
                ->dateTime()
                ->sortable(),


            TextColumn::make('athlete.name')
                ->label('Atleta')
                ->visible($canViewAny),

            TextColumn::make('device.name')->label('Dispositivo')->sortable()->searchable()->toggleable(),
            TextColumn::make('ejercicio')->label('Ejercicio')->searchable()->toggleable(),
            TextColumn::make('repeticiones')->label('Reps')->sortable(),
            TextColumn::make('bpm')->label('BPM')->sortable(),
        ])
        ->filters([
            Filter::make('fecha')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('Desde'),
                    Forms\Components\DatePicker::make('to')->label('Hasta'),
                ])
                ->query(function ($query, array $data) {
                    $from = $data['from'] ?? null;
                    $to   = $data['to']   ?? null;
                    if ($from) {
                        $query->where(function ($q) use ($from) {
                            $q->whereDate('captured_at', '>=', $from)
                              ->orWhereDate('created_at', '>=', $from);
                        });
                    }
                    if ($to) {
                        $query->where(function ($q) use ($to) {
                            $q->whereDate('captured_at', '<=', $to)
                              ->orWhereDate('created_at', '<=', $to);
                        });
                    }
                }),

            SelectFilter::make('athlete_id')
                ->label('Atleta')
                ->options(
                    fn () => User::query()
                        ->whereHas('roles', fn ($q) => $q->where('name', 'atleta'))
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (User $u) => [
                            $u->id => trim(($u->name ?? '').' '.($u->apellidos ?? ''))
                        ])
                        ->toArray()
                )
                ->searchable()
                ->preload()
                ->visible($canViewAny),

            SelectFilter::make('ejercicio')
                ->label('Ejercicio')
                ->options(
                    fn () => DeviceMetric::query()
                        ->whereNotNull('ejercicio')
                        ->distinct()
                        ->orderBy('ejercicio')
                        ->pluck('ejercicio', 'ejercicio')
                        ->toArray()
                ),
        ])
        ->defaultSort('id', 'desc');
}
}
