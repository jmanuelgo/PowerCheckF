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
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\Inicio;


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
            ->homeUrl(function (): string {
                $user = Auth::user();

                // Resto se queda en Inicio (tu Dashboard)
                return Inicio::getUrl();
            })
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
/* === FONDO GENERAL === */
.fi-simple-layout, .fi-auth {
    min-height: 100vh !important;
    background:
        linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url("' . asset('image/20250918_095344.jpg') . '") center center / cover no-repeat fixed !important;
}

/* === CUADRO DEL LOGIN === */
.fi-simple-main {
    background-color: #0a1a3d !important; /* Azul oscuro sólido */
    opacity: 0.85 !important;
    border-radius: 0.75rem !important;
    padding: 3rem 2rem !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
}

/* Anula transparencias heredadas */
.fi-simple-main [class*="bg-white"],
.fi-simple-main [class*="bg-gray"],
.fi-simple-main [class*="bg-opacity"],
.fi-simple-main [style*="background-color"] {
    background: transparent !important;
}

/* === Texto e inputs === */
.fi-simple-main,
.fi-simple-main * {
    color: #f9fafb !important;
}

/* === Botón principal === */
.fi-simple-main button[type="submit"] {
    background-color: #2563eb !important;
    color: #fff !important;
    border: none !important;
}
.fi-simple-main button[type="submit"]:hover {
    background-color: #1d4ed8 !important;
}

/* === Logo === */
.fi-logo img, .fi-logo svg {
    height: 5rem !important;
    width: auto !important;
    display: block !important;
    margin: 0 auto 1rem auto !important;
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
