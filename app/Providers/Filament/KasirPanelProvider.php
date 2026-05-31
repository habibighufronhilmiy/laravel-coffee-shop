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
                    fn (): string => Blade::render('<div class="text-center" style="animation: fadeSlideIn 0.6s ease-out;"><div class="mx-auto w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg flex items-center justify-center mb-5 ring-4 ring-white/60"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-11 h-11 object-contain rounded-lg"></div><h1 style="font-size: 1.5rem; font-weight: 800; color: #111827; margin: 0 0 0.25rem; letter-spacing: -0.02em;">Selamat Datang</h1><p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Panel Kasir Tens Coffee</p></div>'),
                );

                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => Blade::render(<<<'HTML'
                        <style>
                            @keyframes fadeSlideIn {
                                from { opacity: 0; transform: translateY(20px); }
                                to { opacity: 1; transform: translateY(0); }
                            }
                            @keyframes fadeIn {
                                from { opacity: 0; }
                                to { opacity: 1; }
                            }
                            .fi-simple-layout {
                                background: linear-gradient(135deg, #052e16 0%, #064e3b 30%, #059669 70%, #065f46 100%);
                                min-height: 100vh;
                                position: relative;
                                overflow: hidden;
                            }
                            .fi-simple-layout::before {
                                content: '';
                                position: absolute;
                                inset: 0;
                                background:
                                    radial-gradient(ellipse 600px 400px at 20% 30%, rgba(16,185,129,0.08) 0%, transparent 60%),
                                    radial-gradient(ellipse 500px 500px at 80% 70%, rgba(5,150,105,0.06) 0%, transparent 60%),
                                    radial-gradient(ellipse 300px 300px at 50% 50%, rgba(52,211,153,0.04) 0%, transparent 60%);
                                pointer-events: none;
                                animation: fadeIn 1s ease-out;
                            }
                            .fi-simple-layout::after {
                                content: '';
                                position: absolute;
                                inset: 0;
                                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                                pointer-events: none;
                                opacity: 0.5;
                            }
                            .fi-simple-main-ctn {
                                padding: 2rem 1rem;
                                position: relative;
                                z-index: 1;
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
                                animation: fadeSlideIn 0.5s ease-out 0.2s both;
                            }
                            .fi-simple-page-content {
                                background: #ffffff;
                                border-radius: 1.75rem;
                                box-shadow:
                                    0 0 0 1px rgba(255,255,255,0.05),
                                    0 25px 50px -12px rgba(0,0,0,0.4),
                                    0 4px 18px 0 rgba(0,0,0,0.12);
                                padding: 2.5rem 2.5rem 2rem;
                            }
                            .fi-simple-header { display: none; }
                            .fi-form { display: flex; flex-direction: column; gap: 1.25rem; }
                            .fi-form > div { animation: fadeSlideIn 0.5s ease-out 0.3s both; }
                            .fi-form > div:nth-child(2) { animation-delay: 0.35s; }
                            .fi-form > div:nth-child(3) { animation-delay: 0.4s; }
                            .fi-input-wrp {
                                border-radius: 0.75rem !important;
                                overflow: hidden !important;
                                transition: all 0.2s ease !important;
                                box-shadow: 0 1px 3px 0 rgba(0,0,0,0.04) !important;
                            }
                            .fi-input-wrp:focus-within {
                                box-shadow: 0 0 0 4px rgba(16,185,129,0.1), 0 1px 3px 0 rgba(0,0,0,0.04) !important;
                            }
                            .fi-input {
                                border-radius: 0.75rem !important;
                                border: 2px solid #9ca3af !important;
                                padding: 0.85rem 1rem !important;
                                font-size: 0.95rem !important;
                                transition: all 0.2s ease !important;
                                background: #ffffff !important;
                                color: #111827 !important;
                            }
                            .fi-input:hover {
                                border-color: #6b7280 !important;
                            }
                            .fi-input:focus {
                                background: #ffffff !important;
                                border-color: #059669 !important;
                                outline: none !important;
                            }
                            .fi-input::placeholder {
                                color: #9ca3af !important;
                            }
                            .fi-label {
                                font-weight: 600 !important;
                                font-size: 0.875rem !important;
                                color: #1f2937 !important;
                                margin-bottom: 0.375rem !important;
                                display: block !important;
                            }
                            .fi-label-required-prefix {
                                color: #ef4444 !important;
                            }
                            input.fi-checkbox-input {
                                width: 1.125rem !important;
                                height: 1.125rem !important;
                                border-radius: 0.375rem !important;
                                border: 2px solid #9ca3af !important;
                                accent-color: #059669 !important;
                                cursor: pointer !important;
                                flex-shrink: 0 !important;
                            }
                            input.fi-checkbox-input:checked {
                                border-color: #059669 !important;
                            }
                            button[type="submit"] {
                                background: linear-gradient(135deg, #059669, #047857) !important;
                                border-radius: 0.75rem !important;
                                padding: 0.85rem 1.5rem !important;
                                font-weight: 700 !important;
                                font-size: 1rem !important;
                                letter-spacing: 0.01em !important;
                                transition: all 0.2s ease !important;
                                box-shadow: 0 4px 14px -2px rgba(5,150,105,0.4) !important;
                                border: none !important;
                                color: white !important;
                                width: 100% !important;
                                cursor: pointer !important;
                                position: relative !important;
                                overflow: hidden !important;
                            }
                            button[type="submit"]::after {
                                content: '';
                                position: absolute;
                                inset: 0;
                                background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.1) 50%, transparent 60%);
                                opacity: 0;
                                transition: opacity 0.3s;
                            }
                            button[type="submit"]:hover::after { opacity: 1; }
                            button[type="submit"]:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 8px 25px -4px rgba(5,150,105,0.5) !important;
                            }
                            button[type="submit"]:active {
                                transform: translateY(0);
                                box-shadow: 0 4px 12px -2px rgba(5,150,105,0.4) !important;
                            }
                            .fi-form-actions { margin-top: 0.25rem !important; }
                            @media (max-width: 640px) {
                                .fi-simple-page-content { padding: 1.75rem 1.5rem 1.5rem; border-radius: 1.25rem; }
                                .fi-simple-main-ctn { padding: 0.75rem; }
                            }
                        </style>
                    HTML),
                );
            });
    }
}
