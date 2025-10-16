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

    // Icono y etiqueta de navegación que tenías antes
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationLabel = 'Dispositivos';
    protected static ?string $pluralModelLabel = 'Dispositivos'; // Para el título
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
        // El formulario se genera automáticamente.
        // Lo hacemos no editable, ya que los datos vienen del dispositivo.
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
     public static function getPermissionPrefixes(): array // 👈 AÑADE ESTE MÉTODO
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
            'connect',      // <-- Añade tus permisos personalizados aquí también
            'disconnect',
            'forceRelease',
        ];
    }
    public static function table(Table $table): Table
    {
        // Aquí movemos TODA la lógica de la tabla desde DevicesList.php
        return $table
            ->poll('10s') // Mantenemos el sondeo para actualizaciones en tiempo real
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
            ->filters([
                // Puedes añadir filtros si lo necesitas en el futuro
            ])
            ->actions([
    // Acción CONECTAR
    Action::make('conectar')
        ->label('Conectar')->icon('heroicon-o-link')
        // ANTES: Tenías toda la lógica aquí.
        // AHORA: Simplemente le preguntamos a la policy si el usuario "puede" (can) "conectar" este registro. ✅
        ->visible(fn (Device $record): bool => auth()->user()->can('connect', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            // ... la lógica de la acción no cambia ...
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

    // Acción LIBERAR
    Action::make('liberar')
        ->label('Liberar')->icon('heroicon-o-no-symbol')->color('gray')
        // AHORA: Le preguntamos a la policy si el usuario "puede" "desconectar" este registro. ✅
        ->visible(fn (Device $record): bool => auth()->user()->can('disconnect', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            // ... la lógica de la acción no cambia ...
            $active = $r->activeSession()->first();
            if ($active && (int) $active->athlete_id === (int) Auth::id()) {
                $active->update(['status' => 'ended', 'ended_at' => now()]);
                Notification::make()->title('Dispositivo liberado.')->success()->send();
            } else {
                Notification::make()->title('No tienes este dispositivo asignado.')->danger()->send();
            }
        }),

    // Acción FORZAR LIBERACIÓN
    Action::make('forzar_liberacion')
        ->label('Forzar liberación')->icon('heroicon-o-lock-open')->color('danger')
        // AHORA: Le preguntamos a la policy si el usuario "puede" "forzar la liberación" (forceRelease) de este registro. ✅
        ->visible(fn (Device $record): bool => auth()->user()->can('forceRelease', $record))
        ->requiresConfirmation()
        ->action(function (Device $r) {
            // ... la lógica de la acción no cambia ...
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
        return [
            // Aquí podrías añadir relaciones en el futuro, por ejemplo, para ver las sesiones de un dispositivo.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            // Usamos 'view' en lugar de 'edit' porque los datos no se editan manualmente.
            'view' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}