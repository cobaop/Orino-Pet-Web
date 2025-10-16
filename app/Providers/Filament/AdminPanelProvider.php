<?php

namespace App\Providers\Filament;

use App\Http\Middleware\LogUserMiddleware;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\User;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->homeUrl('/admin/dashboard')
            ->brandName('Orino Pet')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                UserMenuItem::make()
                    ->label('Profil')
                    ->icon('heroicon-o-user')
                    ->url('/profile'),

                UserMenuItem::make()
                    ->label('Switch to Owner')
                    ->icon('heroicon-o-user')
                    ->url(fn () => '/owner')
                    ->visible(fn () => auth()->user()?->hasRole(['owner'])),

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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            // ->pages([
            //     Pages\Dashboard::class,
            // ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
                \App\Http\Middleware\FilamentLogoutRedirect::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // NOTE: This will be ignored in multi-panel mode unless you still manually use it
                'can:viewFilament',
            ]);
    }

    /**
     * Override ini adalah kunci agar user bisa akses panel saat production.
     */
    public function canAccessPanel(?User $user): bool
    {
        return $user?->hasAnyRole([
            'super_admin',
            'manager',
            'docter',
            'petshop_employee',
            'clinic_employee',
            'owner',
        ]) ?? false;
    }

    public function boot(): void
    {
        Gate::define('viewFilament', function ($user) {
            return $user->hasAnyRole([
                'super_admin',
                'manager',
                'docter',
                'petshop_employee',
                'clinic_employee',
                'owner',
            ]);
        });
    }

    public function registerNavigationItems(): void
    {
        Filament::registerNavigationItems([
            NavigationItem::make('Reservasi Klinik')
                ->url('/admin/reservasi-klinik')
                ->icon('heroicon-o-clipboard-document-check')
                ->visible(fn () => auth()->user()?->hasAnyRole(['docter', 'clinic_employee'])),

            NavigationItem::make('Layanan Klinik')
                ->url('/admin/layanan-klinik')
                ->icon('heroicon-o-hospital')
                ->visible(fn () => auth()->user()?->hasAnyRole(['docter'])),

            NavigationItem::make('Reservasi Grooming')
                ->url('/admin/reservasi-grooming')
                ->icon('heroicon-o-scissors')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager', 'petshop_employee'])),

            NavigationItem::make('Kuota Grooming')
                ->url('/admin/kuota-grooming')
                ->icon('heroicon-o-chart-pie')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),

            NavigationItem::make('Penitipan Kucing')
                ->url('/admin/penitipan-kucing')
                ->icon('heroicon-o-home')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager', 'petshop_employee'])),

            NavigationItem::make('Paket Grooming')
                ->url('/admin/paket-grooming')
                ->icon('heroicon-o-gift')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),

            NavigationItem::make('Customer')
                ->url('/admin/customers')
                ->icon('heroicon-o-user-group')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager', 'petshop_employee'])),

            NavigationItem::make('Kandang')
                ->url('/admin/kandangs')
                ->icon('heroicon-o-home-modern')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager', 'petshop_employee'])),

            NavigationItem::make('User')
                ->url('/admin/users')
                ->icon('heroicon-o-home-modern')
                ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin'])),

            NavigationItem::make('Activity Log')
                ->url('/admin/activities')
                ->icon('heroicon-o-rectangle-stack')
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
        ]);
    }
}
