<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KasirPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kasir')
            ->path('kasir')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('Tens Coffee Kasir')
            ->discoverResources(in: app_path('Filament/Kasir/Resources'), for: 'App\Filament\Kasir\Resources')
            ->discoverPages(in: app_path('Filament/Kasir/Pages'), for: 'App\Filament\Kasir\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Kasir/Widgets'), for: 'App\Filament\Kasir\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->authGuard('web')
            ->bootUsing(function (Panel $panel): void {
                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                    fn (): string => Blade::render('<div class="text-center mb-6"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="h-16 w-auto mx-auto mb-2"><h1 class="text-2xl font-bold text-gray-800">Tens Coffee</h1><p class="text-sm text-gray-500">Panel Kasir</p></div>'),
                );
            });
    }
}
