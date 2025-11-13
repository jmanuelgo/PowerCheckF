<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RankingResource\Pages;
use App\Filament\Resources\RankingResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Entrenador;
use App\Models\Atleta;
use App\Models\VideoAnalysis;


class RankingResource extends Resource
{
    protected static ?string $model = VideoAnalysis::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Ranking de Atletas';
    protected static ?string $slug = 'ranking-atletas';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'entrenador']);
    }
    public static function getPluralModelLabel(): string
    {
        return 'Ranking de Atletas';
    }

public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('weight', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                if ($user->hasAnyRole(['super_admin', 'admin'])) {
                }
                elseif ($user->hasRole('entrenador')) {

                    $entrenador = Entrenador::where('user_id', $user->id)->first();

                    if ($entrenador) {

                        $athleteUserIds = Atleta::where('entrenador_id', $entrenador->id)
                                              ->pluck('user_id');
                        $query->whereIn('user_id', $athleteUserIds);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }
                $query->where('status', 'done');
            })
            // --- FIN DE LA LÓGICA CORREGIDA ---

            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Atleta')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('movement')->label('Ejercicio')->badge()
                    ->formatStateUsing(fn(string $state) => ['squat' => 'Sentadilla', 'bench' => 'Press banca', 'deadlift' => 'Peso muerto'][$state] ?? $state)
                    ->searchable(),

                Tables\Columns\TextColumn::make('weight')->label('Peso (kg)')
                    ->sortable()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('efficiency_pct')->label('Eficiencia (%)')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state !== null ? number_format((float)$state, 2) : '—')
                    ->color(fn($state) => $state === null ? null : ((float)$state >= 90 ? 'success' : ((float)$state >= 80 ? 'warning' : 'danger'))),

                Tables\Columns\TextColumn::make('analyzed_at')->label('Fecha análisis')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('movement')
                    ->label('Ejercicio')
                    ->options([
                        'squat' => 'Sentadilla',
                        'bench' => 'Press banca',
                        'deadlift' => 'Peso muerto',
                    ])
            ])
            ->actions([
                Tables\Actions\Action::make('ver_analisis')
                    ->label('Ver')
                    ->icon('heroicon-o-magnifying-glass')
                    ->url(fn(VideoAnalysis $record): string => VideoAnalysisResource::getUrl('result', ['record' => $record])),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRankings::route('/'),
        ];
    }
}
