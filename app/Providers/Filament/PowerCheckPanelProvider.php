<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Filament\Navigation\UserMenuItem;
use App\Filament\Pages\ConfigurarPerfil;

class PowerCheckPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('powerCheck')
            ->path('powerCheck')

            // Activa las pantallas de login / perfil:
            ->login()
            ->profile()

            // ======= BRAND / LOGO =======
            // Esto reemplaza el texto "PowerCheck" por el logo, también en el login:
            ->brandLogo(asset('image/powercheckLogo2.png'))
            ->brandLogoHeight('5rem')

            // Paleta del panel
            ->colors([
                'primary' => Color::hex('#2563eb'),
            ])

            // Tu tema (lo tienes configurado ya)
            ->viteTheme('resources/css/filament/powercheck/theme.css')

            // ======= FONDO SOLO EN LOGIN (sin publicar vistas) =======
            // Inyectamos CSS únicamente en la vista de login usando un hook:
            ->renderHook(
                'panels::auth.login.form.after',
                fn(): string => '<style>
        /* --- FONDO (lo que ya te funciona) --- */
        .fi-simple-main, .fi-simple-layout, .fi-auth {
            min-height: 100vh;
            background:
                linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
                url("' . asset('image/20250918_095344.jpg') . '") center center / cover no-repeat fixed;
        }

        /* --- CARD SÓLIDO (selectores más fuertes y capa interna) --- */

        /* Cubre TODOS los posibles contenedores del card de auth */
        .fi-auth .fi-auth-card,
        .fi-auth .fi-card,
        .fi-simple-main .fi-card {
            position: relative !important;
            border-radius: 0.75rem !important;
            overflow: hidden;            /* para que el ::before respete el borde redondeado */
            isolation: isolate;          /* crea un stacking context seguro */
        }

        /* Si Filament mete un wrapper interno con bg translúcido, neutralízalo */
        .fi-auth .fi-card > div[class*="bg-"],
        .fi-auth .fi-card[class*="bg-"] {
            background-color: transparent !important;
        }

        /* Lámina sólida detrás del contenido del card (gana a cualquier bg-*/
        .fi-auth .fi-auth-card::before,
        .fi-auth .fi-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: #1f2937;                 /* gris-800 sólido */
            box-shadow: 0 10px 30px rgba(0,0,0,.6);
            z-index: 0;                           /* queda detrás del contenido */
        }

        /* Garantiza que el contenido quede por encima y clickeable */
        .fi-auth .fi-auth-card > *,
        .fi-auth .fi-card > * {
            position: relative;
            z-index: 1;
        }

        /* Texto legible dentro del card */
        .fi-auth .fi-auth-card,
        .fi-auth .fi-auth-card * {
            color: #f9fafb;
        }

        /* Logo más grande (coincide con brandLogoHeight) */
        .fi-logo img, .fi-logo svg {
            height: 5rem !important;
            width: auto !important;
        }
    </style>'
            )



            ->userMenuItems([
                \Filament\Navigation\UserMenuItem::make()
                    ->label('Configurar perfil')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn() => \App\Filament\Pages\ConfigurarPerfil::getUrl()),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \Filament\Widgets\AccountWidget::class,
            ])
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Filament\Http\Middleware\AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \Filament\Http\Middleware\DisableBladeIconComponents::class,
                \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
            ]);
    }
}
