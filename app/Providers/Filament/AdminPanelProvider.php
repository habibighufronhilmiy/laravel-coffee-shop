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
                    fn (): string => Blade::render('<div class="text-center mb-8"><div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center mb-4 ring-4 ring-white/50"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-12 h-12 object-contain rounded-lg"></div><h1 class="text-2xl font-bold text-white">Selamat Datang</h1><p class="text-sm text-white/80 mt-1">Panel Admin Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render(<<<'HTML'
                        <style>
                            .fi-simple-layout {
                                background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 30%, #1d4ed8 70%, #1e3a5f 100%);
                                min-height: 100vh;
                                position: relative;
                            }
                            .fi-simple-layout::before {
                                content: '';
                                position: absolute;
                                top: -50%; left: -50%;
                                width: 200%; height: 200%;
                                background: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.12) 0%, transparent 50%),
                                            radial-gradient(ellipse at 80% 50%, rgba(139,92,246,0.08) 0%, transparent 50%);
                                pointer-events: none;
                            }
                            .fi-simple-main-ctn { padding: 2rem 1rem; position: relative; z-index: 1; }
                            .fi-simple-main { width: 100%; max-width: 420px; margin: 0 auto; }
                            .fi-simple-page { background: transparent !important; box-shadow: none !important; }
                            .fi-simple-page-content {
                                background: rgba(15, 23, 42, 0.6);
                                backdrop-filter: blur(20px);
                                border-radius: 1.5rem;
                                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
                                padding: 2.5rem;
                                border: 1px solid rgba(255,255,255,0.15);
                            }
                            .fi-simple-header { display: none; }
                            .fi-input {
                                border-radius: 0.75rem !important;
                                border: 2px solid rgba(255,255,255,0.2) !important;
                                padding: 0.75rem 1rem !important;
                                font-size: 0.95rem !important;
                                transition: all 0.2s ease !important;
                                background: rgba(255,255,255,0.9) !important;
                            }
                            .fi-input:focus {
                                border-color: #3b82f6 !important;
                                box-shadow: 0 0 0 4px rgba(59,130,246,0.2) !important;
                                outline: none !important;
                            }
                            .fi-label { font-weight: 600 !important; font-size: 0.875rem !important; color: #e5e7eb !important; margin-bottom: 0.25rem !important; }
                            button[type="submit"] {
                                background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
                                border-radius: 0.75rem !important;
                                padding: 0.8rem 1.5rem !important;
                                font-weight: 700 !important;
                                font-size: 0.95rem !important;
                                transition: all 0.2s ease !important;
                                box-shadow: 0 4px 14px -2px rgba(37,99,235,0.4) !important;
                                border: none !important;
                                color: white !important;
                                width: 100% !important;
                            }
                            button[type="submit"]:hover {
                                transform: translateY(-1px);
                                box-shadow: 0 6px 20px -2px rgba(37,99,235,0.5) !important;
                            }
                            button[type="submit"]:active { transform: translateY(0); }
                            .fi-form { display: flex; flex-direction: column; gap: 1.25rem; }
                            @media (max-width: 640px) {
                                .fi-simple-page-content { padding: 1.5rem; border-radius: 1rem; }
                                .fi-simple-main-ctn { padding: 1rem 0.5rem; }
                            }
                        </style>
                    HTML),
                );
            });
    }
}
