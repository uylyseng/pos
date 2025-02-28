<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\LocalFontProvider;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Dashboard;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->font(
                'inter',
                url: asset('css/fonts.css'),
                provider: LocalFontProvider::class,
            )
            ->brandName('សួស្តីកាហ្វេ ភីហ្សានិងនំខេក')
//            ->brandLogo(asset('images/brand.jpg'))
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Green,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                ActivitylogPlugin::make()
                    ->label('Activity Log')
                    ->pluralLabel('Activity Logs')
                    ->navigationIcon('heroicon-o-clipboard-document-list')
                    ->navigationGroup('Activity Management')
                    ->navigationSort(99)
                    ->navigationItem(true)
                    ->authorize(fn () => auth()->user()->can('view_any_activitylog')),
            ])
            ->navigationItems([
                NavigationItem::make('POS')
                    ->url('/pos', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-clipboard-document-list')
                    ->visible(fn () => auth()->user()->can('access_pos'))
                    ->sort(1),
            ]);
    }
}
