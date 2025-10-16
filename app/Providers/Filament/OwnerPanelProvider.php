<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\UserMenuItem;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Http\Middleware\FilamentLogoutRedirect;
use App\Http\Middleware\LogUserMiddleware;
use Illuminate\Auth\Middleware\Authenticate;

class OwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('owner')
            ->path('owner')
            ->homeUrl('/owner')
            ->brandName('Orino Pet')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                UserMenuItem::make()
                    ->label('Profil')
                    ->icon('heroicon-o-user')
                    ->url('/profile/'),

                UserMenuItem::make()
                    ->label('Switch to Super Admin')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->url('/admin/dashboard'),

                UserMenuItem::make()
                    ->label('Beranda')
                    ->icon('heroicon-o-home')
                    ->url('/'),

                UserMenuItem::make()
                    ->label('Help')
                    ->icon('heroicon-o-question-mark-circle')
                    ->url('/admin/help'),
            ])

            ->login()
            ->discoverResources(
                in: app_path('Filament/Owner/Resources'),
                for: 'App\\Filament\\Owner\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Owner/Pages'),
                for: 'App\\Filament\\Owner\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Owner/Widgets'),
                for: 'App\\Filament\\Owner\\Widgets'
            )
            ->middleware([
                'web',
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                LogUserMiddleware::class,
                FilamentLogoutRedirect::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['owner']);
    }
}
