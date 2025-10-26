<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use App\Models\DeviceSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class DeviceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Device::class;
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationLabel = 'Dispositivos';
    protected static ?string $pluralModelLabel = 'Dispositivos';
    protected static ?int $navigationSort = 98; 
    public static function getPermissions(): array
    {
        return [
            'view_any',
            'view',
            'connect',
            'disconnect',
            'forceRelease',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('ip')
                    ->maxLength(45)
                    ->disabled(),
                Forms\Components\DateTimePicker::make('last_seen')
                    ->disabled(),
            ]);
    }
     public static function getPermissionPrefixes(): array 
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'connect',   
            'disconnect',
            'forceRelease',
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->poll('10s') 
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('ip')->label('IP')->copyable()->sortable(),
                TextColumn::make('last_seen')->label('Último ping')->since()->sortable(),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn (Device $r) => $r->is_available ? 'Disponible' : 'Offline')
                    ->colors([
                        'success' => fn (Device $r) => $r->is_available,
                        'gray'    => fn (Device $r) => ! $r->is_available,
                    ]),
                BadgeColumn::make('ocupado')
                    ->label('Ocupación')
                    ->formatStateUsing(fn (Device $r) => optional($r->activeSession()->first())->athlete_id ? 'Ocupado' : 'Libre')
                    ->colors([
                        'primary' => fn (Device $r) => optional($r->activeSession()->first())->athlete_id !== null,
                        'info'    => fn (Device $r) => optional($r->activeSession()->first())->athlete_id === null,
                    ]),
            ])
            ->filters([])
            ->actions([
    // Acción CONECTAR
    Action::make('conectar')
        ->label('Conectar')->icon('heroicon-o-link')
        ->visible(fn (Device $record): bool => auth()->user()->can('connect', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            if (optional($r->activeSession()->first())->athlete_id) {
                Notification::make()->title('Este dispositivo ya está ocupado.')->danger()->send();
                return;
            }
            DeviceSession::create([
                'device_id'    => $r->id,
                'athlete_id'   => Auth::id(),
                'status'       => 'active',
                'started_at'   => Carbon::now(),
                'last_seen_at' => Carbon::now(),
                'expires_at'   => Carbon::now()->addMinutes(30),
            ]);
            Notification::make()->title('¡Dispositivo conectado!')->success()->send();
        }),

    Action::make('liberar')
        ->label('Liberar')->icon('heroicon-o-no-symbol')->color('gray')
        ->visible(fn (Device $record): bool => auth()->user()->can('disconnect', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            $active = $r->activeSession()->first();
            if ($active && (int) $active->athlete_id === (int) Auth::id()) {
                $active->update(['status' => 'ended', 'ended_at' => now()]);
                Notification::make()->title('Dispositivo liberado.')->success()->send();
            } else {
                Notification::make()->title('No tienes este dispositivo asignado.')->danger()->send();
            }
        }),

    Action::make('forzar_liberacion')
        ->label('Forzar liberación')->icon('heroicon-o-lock-open')->color('danger')
        ->visible(fn (Device $record): bool => auth()->user()->can('forceRelease', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            $active = $r->activeSession()->first();
            if ($active) {
                $active->update(['status' => 'ended', 'ended_at' => now()]);
                Notification::make()->title('Dispositivo liberado por admin.')->success()->send();
            } else {
                Notification::make()->title('El dispositivo ya estaba libre.')->warning()->send();
            }
        }),
    ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'view' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}