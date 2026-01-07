<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\EnsureUserIsAdmin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->brandLogo(asset('logos/talents_lab_logo.png'))
            ->darkModeBrandLogo(asset('logos/talents_lab_logo.png'))
            ->brandLogoHeight('32px')
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('mini-logo.png'))
            ->sidebarWidth('280px')
            ->renderHook(
                'panels::styles.after',
                fn () => Blade::render('
                    <style>
                        .fi-sidebar {
                            border-right: 1px solid rgb(229 231 235);
                            background: linear-gradient(180deg, #fefefe 0%, #f8fafc 100%);
                        }
                        .dark .fi-sidebar {
                            border-right: 1px solid rgb(55 65 81);
                            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
                        }
                        .fi-sidebar-nav-groups {
                            padding: 0.5rem;
                        }
                        .fi-sidebar-group-label {
                            font-weight: 600;
                            text-transform: uppercase;
                            font-size: 0.7rem;
                            letter-spacing: 0.05em;
                            color: rgb(107 114 128);
                            padding: 0.75rem 0.5rem 0.5rem;
                        }
                        .fi-sidebar-item {
                            border-radius: 0.5rem;
                            margin: 0.125rem 0;
                        }
                        .fi-sidebar-item-button {
                            border-radius: 0.5rem;
                        }
                        .fi-sidebar-item-active .fi-sidebar-item-button {
                            background: rgb(251 191 36 / 0.15);
                            border-left: 3px solid rgb(245 158 11);
                        }
                    </style>
                ')
            )
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Аналитика')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Кандидаты')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Настройки')
                    ->collapsed(true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ]);
    }
}
