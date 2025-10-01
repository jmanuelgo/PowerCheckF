<?php

namespace App\Filament\Pages;

use App\Models\Device;
use App\Models\DeviceSession;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class DevicesList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-signal';
    protected static ?string $navigationLabel = 'Dispositivos';
    protected static ?string $title           = 'Dispositivos';
    protected static ?string $slug            = 'devices-list';
    protected static string $view             = 'filament.pages.devices-list';

    // Mostrar en el menú sólo si tiene permiso (vía Gate)
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Gate::allows('devices.view_available');
    }

    // Bloquear acceso directo por URL si no tiene permiso (vía Gate)
    public static function canAccess(): bool
    {
        return Auth::check() && Gate::allows('devices.view_available');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Device::query()->orderByDesc('last_seen'))
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
            ->actions([
                Action::make('conectar')
                    ->label('Conectar')->icon('heroicon-o-link')
                    ->visible(function (Device $r) {
                        return Auth::check()
                            && Gate::allows('devices.connect')
                            && $r->is_available
                            && ! optional($r->activeSession()->first())->athlete_id;
                    })
                    ->requiresConfirmation()
                    ->action(function (Device $r) {
                        Gate::authorize('connect', $r);

                        if (optional($r->activeSession()->first())->athlete_id) {
                            Notification::make()->title('Este dispositivo ya está ocupado.')->danger()->send();
                            return;
                        }

                        DeviceSession::create([
                            'device_id'    => $r->id,
                            'athlete_id'   => Auth::id(), // cambia a user_id si aplica
                            'status'       => 'active',
                            'started_at'   => Carbon::now(),
                            'last_seen_at' => Carbon::now(),
                            'expires_at'   => Carbon::now()->addMinutes(30),
                        ]);

                        Notification::make()->title('¡Dispositivo conectado!')->success()->send();
                    }),

                Action::make('liberar')
                    ->label('Liberar')->icon('heroicon-o-no-symbol')->color('gray')
                    ->visible(function (Device $r) {
                        $active = $r->activeSession()->first();
                        return Auth::check()
                            && Gate::allows('devices.disconnect')
                            && $active
                            && (int) $active->athlete_id === (int) Auth::id();
                    })
                    ->requiresConfirmation()
                    ->action(function (Device $r) {
                        Gate::authorize('disconnect', $r);

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
                    ->visible(fn () => Auth::check() && Gate::allows('devices.force_release'))
                    ->requiresConfirmation()
                    ->action(function (Device $r) {
                        Gate::authorize('forceRelease', $r);
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
}
