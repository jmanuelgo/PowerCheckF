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
            ->login()
            ->profile()
            ->brandLogo(asset('image/powercheckLogo2.png'))
            ->brandLogoHeight('5rem')
            ->colors([
                'primary' => Color::hex('#2563eb'),
            ])
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
            ->renderHook(
                'panels::body.end',
                fn(): string => <<<HTML
<style>
/* Flecha para el trigger del "registros por página" en la paginación de Filament Tables */
.pc-per-page-trigger {
    position: relative !important;
    padding-right: 2rem !important; /* espacio para la flecha */
}

.pc-per-page-trigger::after {
    content: "";
    position: absolute;
    right: .6rem;
    top: 50%;
    transform: translateY(-50%);
    width: .9rem;
    height: .9rem;
    pointer-events: none;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    /* flecha blanca hacia abajo; cambia %23fff a %23000 si usas tema claro */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M6 9l6 6 6-6' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
}
</style>

<script>
(() => {
  // Marca como trigger (para mostrar flecha) el botón del "per page" en cada tabla
  const markTriggers = () => {
    // Todas las paginaciones de tablas (v3 suele usar fi-ta-..., pero por si acaso, cubrimos .fi-pagination también)
    document.querySelectorAll('.fi-ta-pagination, .fi-pagination').forEach(pag => {
      // Busca botones/drops que contengan solo números (10, 25, 50, 100)
      const candidates = pag.querySelectorAll('button, .fi-dropdown-trigger, [role="button"]');
      candidates.forEach(el => {
        const txt = (el.textContent || '').trim();
        if (/^(10|25|50|100)$/.test(txt) && !el.classList.contains('pc-per-page-trigger')) {
          el.classList.add('pc-per-page-trigger');
        }
      });
    });
  };

  // Ejecuta al cargar
  window.addEventListener('load', markTriggers);

  // Re-ejecuta cuando Livewire/Alpine re-renderizan (paginación, filtros, etc.)
  const obs = new MutationObserver(markTriggers);
  obs.observe(document.documentElement, { childList: true, subtree: true });

  // Filament/Livewire navegación suave
  document.addEventListener('livewire:navigated', markTriggers);
})();
</script>
HTML
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
                \App\Filament\Pages\Inicio::class,   // <- en lugar de \Filament\Pages\Dashboard::class
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // sin widgets
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
