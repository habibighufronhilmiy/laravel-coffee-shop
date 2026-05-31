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
                    fn (): string => Blade::render('<div class="text-center"><div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center mb-5 ring-4 ring-white/60"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-11 h-11 object-contain rounded-lg"></div><h1 class="text-2xl font-extrabold" style="color: #000; margin: 0 0 0.25rem;">Selamat Datang</h1><p style="font-size: 0.9rem; color: #4b5563; margin: 0 0 1.25rem;">Panel Admin Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render(<<<'HTML'
                        <style>
                            @keyframes slideUp {
                                from { opacity: 0; transform: translateY(30px); }
                                to { opacity: 1; transform: translateY(0); }
                            }
                            @keyframes pulseGlow {
                                0%, 100% { opacity: 0.5; }
                                50% { opacity: 0.8; }
                            }
                            .fi-simple-layout {
                                background: linear-gradient(135deg, #0b1120 0%, #172554 40%, #1e40af 70%, #172554 100%);
                                min-height: 100vh;
                                position: relative;
                                overflow: hidden;
                            }
                            .fi-simple-layout::before {
                                content: '';
                                position: absolute;
                                width: 600px;
                                height: 600px;
                                top: -100px;
                                right: -150px;
                                background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 60%);
                                pointer-events: none;
                                animation: pulseGlow 4s ease-in-out infinite;
                            }
                            .fi-simple-layout::after {
                                content: '';
                                position: absolute;
                                width: 400px;
                                height: 400px;
                                bottom: -100px;
                                left: -100px;
                                background: radial-gradient(circle, rgba(37,99,235,0.1) 0%, transparent 60%);
                                pointer-events: none;
                                animation: pulseGlow 5s ease-in-out infinite 1s;
                            }
                            .fi-simple-main-ctn {
                                padding: 2rem 1rem;
                                min-height: 100vh;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                position: relative;
                                z-index: 1;
                            }
                            .fi-simple-main {
                                width: 100%;
                                max-width: 420px;
                                margin: 0 auto;
                            }
                            .fi-simple-page {
                                background: transparent !important;
                                box-shadow: none !important;
                                animation: slideUp 0.6s ease-out;
                            }
                            .fi-simple-page-content {
                                background: #ffffff;
                                border-radius: 1.5rem;
                                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
                                padding: 2.5rem;
                                position: relative;
                            }
                            .fi-simple-page-content::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: 3rem;
                                right: 3rem;
                                height: 4px;
                                background: linear-gradient(90deg, #2563eb, #818cf8, #2563eb);
                                border-radius: 0 0 4px 4px;
                            }
                            .fi-simple-header { display: none; }
                            .fi-form { display: flex; flex-direction: column; gap: 1.25rem; }
                            .fi-input-wrp {
                                border-radius: 0.75rem !important;
                                overflow: hidden !important;
                                transition: box-shadow 0.2s ease !important;
                            }
                            .fi-input-wrp:focus-within {
                                box-shadow: 0 0 0 3px rgba(37,99,235,0.15) !important;
                            }
                            .fi-input {
                                border-radius: 0.75rem !important;
                                border: 2px solid #d1d5db !important;
                                padding: 0.8rem 1rem !important;
                                font-size: 0.95rem !important;
                                transition: border-color 0.2s ease, background 0.2s ease !important;
                                background: #f9fafb !important;
                                color: #111827 !important;
                            }
                            .fi-input:hover {
                                border-color: #9ca3af !important;
                                background: #ffffff !important;
                            }
                            .fi-input:focus {
                                border-color: #3b82f6 !important;
                                background: #ffffff !important;
                                outline: none !important;
                            }
                            .fi-input::placeholder {
                                color: #9ca3af !important;
                            }
                            .fi-fo-field-label,
                            .fi-fo-field-label-content {
                                font-weight: 600 !important;
                                font-size: 0.875rem !important;
                                color: #111827 !important;
                            }
                            .fi-fo-field-label-required-mark {
                                color: #dc2626 !important;
                            }
                            .fi-checkbox-input {
                                width: 1.125rem !important;
                                height: 1.125rem !important;
                                border-radius: 0.375rem !important;
                                border: 2px solid #d1d5db !important;
                                accent-color: #3b82f6 !important;
                                cursor: pointer !important;
                            }
                            button[type="submit"] {
                                background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
                                border-radius: 0.75rem !important;
                                padding: 0.85rem 1.5rem !important;
                                font-weight: 700 !important;
                                font-size: 1rem !important;
                                letter-spacing: 0.01em !important;
                                transition: all 0.25s ease !important;
                                box-shadow: 0 4px 14px 0 rgba(37,99,235,0.3) !important;
                                border: none !important;
                                color: #ffffff !important;
                                width: 100% !important;
                                cursor: pointer !important;
                            }
                            button[type="submit"]:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 8px 25px 0 rgba(37,99,235,0.35) !important;
                            }
                            button[type="submit"]:active {
                                transform: translateY(0);
                            }
                            .fi-form-actions {
                                margin-top: 0.5rem !important;
                            }
                            @media (max-width: 640px) {
                                .fi-simple-page-content { padding: 1.75rem; }
                                .fi-simple-main-ctn { padding: 0.75rem; }
                                .fi-simple-page-content::before { left: 2rem; right: 2rem; }
                            }
                        </style>
                    HTML),
                );
            });
    }
}
