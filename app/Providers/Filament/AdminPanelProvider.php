<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Models\UndanganAktif;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->bootUsing(fn () => app()->setLocale('id'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName(function (): string {
                $couple = once(fn () => UndanganAktif::acaraAktif()?->mempelai);

                return $couple ? $couple->nama_pria.' & '.$couple->nama_wanita : 'Admin Undangan';
            })
            ->brandLogo(fn (): View => view('filament.components.admin-brand'))
            ->font('Source Sans 3', provider: GoogleFontProvider::class)
            ->serifFont('Cormorant Garamond', provider: GoogleFontProvider::class)
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): View => view('filament.components.admin-topbar', [
                    'adminName' => filament()->getUserName(filament()->auth()->user()),
                ]),
            )
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::hex('#681C25'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->darkMode(false)
            ->themeSwitcher(false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
