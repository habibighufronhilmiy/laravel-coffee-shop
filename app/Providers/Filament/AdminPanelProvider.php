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
                    fn (): string => Blade::render('<div class="text-center"><div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center mb-5 ring-4 ring-white/60"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-11 h-11 object-contain rounded-lg"></div><h1 style="font-size: 1.5rem; font-weight: 800; color: #000000; margin: 0 0 0.25rem;">Selamat Datang</h1><p style="font-size: 0.875rem; color: #374151; margin: 0;">Panel Admin Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render(<<<'HTML'
                        <style>
                            .fi-simple-layout {
                                background: linear-gradient(135deg, #0b1120 0%, #172554 40%, #1e40af 70%, #172554 100%);
                                min-height: 100vh;
                            }
                            .fi-simple-main-ctn {
                                padding: 2rem 1rem;
                                min-height: 100vh;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }
                            .fi-simple-main {
                                width: 100%;
                                max-width: 420px;
                                margin: 0 auto;
                            }
                            .fi-simple-page {
                                background: transparent !important;
                                box-shadow: none !important;
                            }
                            .fi-simple-page-content {
                                background: #ffffff;
                                border-radius: 1.5rem;
                                box-shadow: 0 20px 60px -12px rgba(0,0,0,0.3);
                                padding: 2.5rem;
                            }
                            .fi-simple-header { display: none; }
                            .fi-form { display: flex; flex-direction: column; gap: 1.25rem; }
                            .fi-input-wrp {
                                border-radius: 0.75rem !important;
                                overflow: hidden !important;
                            }
                            .fi-input {
                                border-radius: 0.75rem !important;
                                border: 2px solid #6b7280 !important;
                                padding: 0.8rem 1rem !important;
                                font-size: 0.95rem !important;
                                background: #ffffff !important;
                                color: #000000 !important;
                            }
                            .fi-input:focus {
                                border-color: #3b82f6 !important;
                                outline: none !important;
                            }
                            .fi-fo-field-label,
                            .fi-fo-field-label-content {
                                font-weight: 600 !important;
                                font-size: 0.875rem !important;
                                color: #000000 !important;
                            }
                            .fi-fo-field-label-required-mark {
                                color: #dc2626 !important;
                            }
                            .fi-checkbox-input {
                                width: 1.125rem !important;
                                height: 1.125rem !important;
                                border-radius: 0.375rem !important;
                                border: 2px solid #6b7280 !important;
                                accent-color: #3b82f6 !important;
                            }
                            button[type="submit"] {
                                background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
                                border-radius: 0.75rem !important;
                                padding: 0.8rem 1.5rem !important;
                                font-weight: 700 !important;
                                font-size: 1rem !important;
                                border: none !important;
                                color: #ffffff !important;
                                width: 100% !important;
                                cursor: pointer !important;
                            }
                            button[type="submit"]:hover {
                                opacity: 0.9;
                            }
                            @media (max-width: 640px) {
                                .fi-simple-page-content { padding: 1.5rem; }
                                .fi-simple-main-ctn { padding: 0.75rem; }
                            }
                        </style>
                    HTML),
                );
            });
    }
}
