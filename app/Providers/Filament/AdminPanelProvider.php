<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\LatestOrders;
use App\Filament\Admin\Widgets\StatsOverview;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('Tens Coffee Admin')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverview::class,
                LatestOrders::class,
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
                    fn (): string => Blade::render('<div id="login-header" class="mb-6"><div class="logo-wrap" style="width:5rem;height:5rem;border-radius:1rem;background:linear-gradient(135deg,#2563eb,#818cf8);box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);margin:0 auto 1rem;outline:4px solid white"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" style="width:2.75rem;height:2.75rem;object-fit:contain;border-radius:0.5rem"></div><h1 style="font-size:1.5rem;font-weight:800;color:#000;margin:0 0 0.25rem">Selamat Datang</h1><p style="font-size:0.875rem;color:#374151;margin:0">Panel Admin Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render('<style>.fi-simple-header{display:none}.fi-simple-layout{background:#f1f5f9}.fi-simple-page-content{background:#fff;border-radius:1rem;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid #e2e8f0}#login-header{text-align:center}#login-header .logo-wrap{display:inline-flex;align-items:center;justify-content:center}#login-header h1{color:#000!important}#login-header p{color:#374151!important}</style>'),
                );
            });
    }
}
