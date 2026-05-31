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
                    fn (): string => Blade::render('<div class="text-center"><div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-400 rounded-2xl shadow-lg flex items-center justify-center mb-5 ring-4 ring-blue-100"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-11 h-11 object-contain rounded-lg"></div><h1 class="text-2xl font-extrabold" style="color: #1e293b; margin: 0 0 0.25rem;">Selamat Datang</h1><p style="font-size: 0.9rem; color: #475569; margin: 0 0 1.25rem;">Panel Admin Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render(<<<'HTML'
                        <style>
                            @keyframes fadeUp {
                                from { opacity: 0; transform: translateY(12px); }
                                to { opacity: 1; transform: translateY(0); }
                            }
                            .fi-simple-layout {
                                background: #f8fafc;
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
                                animation: fadeUp 0.5s ease-out;
                            }
                            .fi-simple-page-content {
                                background: #ffffff;
                                border-radius: 1.25rem;
                                box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
                                padding: 2.5rem;
                                border: 1px solid #e2e8f0;
                            }
                            .fi-sc-form { display: flex; flex-direction: column; gap: 1.25rem; }
                            .fi-input-wrp {
                                border-radius: 0.625rem !important;
                                overflow: hidden !important;
                                transition: box-shadow 0.2s !important;
                            }
                            .fi-input-wrp:focus-within {
                                box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important;
                            }
                            .fi-input {
                                border-radius: 0.625rem !important;
                                border: 1.5px solid #cbd5e1 !important;
                                padding: 0.75rem 1rem !important;
                                font-size: 0.9375rem !important;
                                transition: border-color 0.2s, background 0.2s !important;
                                background: #ffffff !important;
                                color: #0f172a !important;
                            }
                            .fi-input:hover {
                                border-color: #94a3b8 !important;
                            }
                            .fi-input:focus {
                                border-color: #2563eb !important;
                                outline: none !important;
                            }
                            .fi-input::placeholder {
                                color: #94a3b8 !important;
                            }
                            .fi-fo-field-label,
                            .fi-fo-field-label-content {
                                font-weight: 600 !important;
                                font-size: 0.875rem !important;
                                color: #0f172a !important;
                            }
                            .fi-fo-field-label-required-mark {
                                color: #ef4444 !important;
                            }
                            .fi-checkbox-input {
                                width: 1.125rem !important;
                                height: 1.125rem !important;
                                border-radius: 0.375rem !important;
                                border: 1.5px solid #cbd5e1 !important;
                                accent-color: #2563eb !important;
                                cursor: pointer !important;
                            }
                            button[type="submit"] {
                                background: #2563eb !important;
                                border-radius: 0.625rem !important;
                                padding: 0.75rem 1.5rem !important;
                                font-weight: 600 !important;
                                font-size: 0.9375rem !important;
                                transition: all 0.2s !important;
                                border: none !important;
                                color: #ffffff !important;
                                width: 100% !important;
                                cursor: pointer !important;
                            }
                            button[type="submit"]:hover {
                                background: #1d4ed8 !important;
                            }
                            .fi-sc-actions {
                                margin-top: 0.25rem !important;
                            }
                            @media (max-width: 640px) {
                                .fi-simple-page-content { padding: 1.75rem; }
                                .fi-simple-main-ctn { padding: 0.75rem; }
                            }
                        </style>
                    HTML),
                );
            });
    }
}
