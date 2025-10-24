<?php
// app/Filament/Resources/VideoAnalysisResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\VideoAnalysisResource\Pages;
use App\Models\VideoAnalysis;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoAnalysisResource extends Resource
{
    protected static ?string $model = VideoAnalysis::class;
    protected static ?string $navigationLabel = 'Análisis de video';
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('user_id', auth()->id())->where('status', 'done'))
            ->columns([
                Tables\Columns\TextColumn::make('movement')->label('Ejercicio')->badge()
                    ->formatStateUsing(fn(string $state) => ['squat' => 'Sentadilla', 'bench' => 'Press banca', 'deadlift' => 'Peso muerto'][$state] ?? $state),
                Tables\Columns\TextColumn::make('analyzed_at')->label('Fecha análisis')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('efficiency_pct')->label('Eficiencia (%)')
                    ->formatStateUsing(fn($state) => $state !== null ? number_format((float)$state, 2) : '—')
                    ->color(fn($state) => $state === null ? null : ((float)$state >= 90 ? 'success' : ((float)$state >= 80 ? 'warning' : 'danger'))),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_detalles')
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-document-magnifying-glass') // Icono adecuado para ver detalles
                    ->url(fn(VideoAnalysis $record): string => static::getUrl('result', ['record' => $record])),
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->download_url ? route('video.proxyDownload', ['url' => $record->download_url]) : null)
                    ->visible(fn($record) => filled($record->download_url))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideoAnalyses::route('/'),
            'pick-bar'  => Pages\PickBar::route('/{record}/pick-bar'),
            'pick-full' => Pages\PickFull::route('/{record}/pick-full'),
            'result'    => Pages\Result::route('/{record}/result'),
        ];
    }
}
