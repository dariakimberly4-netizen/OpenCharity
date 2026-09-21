<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\SystemSettings;
use App\Http\Middleware\ApplyActiveScope;
use App\Http\Middleware\ApplyUserCaseTypeScope;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\SettingsPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Statikbe\FilamentTranslationManager\FilamentChainedTranslationManagerPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->homeUrl('/')
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->profile()
            ->topbar(false)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Government Payout Workflow')->label(__('Government Payout Workflow')),
                NavigationGroup::make('Assistance')->label(__('Assistance')),
                NavigationGroup::make('Families')->label(__('Families')),
                NavigationGroup::make('Cases')->label(__('Cases')),
                NavigationGroup::make('Donations')->label(__('Donations')),
                NavigationGroup::make('Documents')->label(__('Documents')),
                NavigationGroup::make('Settings')->label(__('Settings')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->when(config('app.demo_mode'), fn (Panel $panel) => $panel
                ->renderHook(
                    PanelsRenderHook::BODY_START,
                    fn (): View => view('demo-banner'),
                )
            )
            ->plugins([
                ...config('app.demo_mode') ? [] : [FilamentChainedTranslationManagerPlugin::make()],
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                EasyFooterPlugin::make()->withBorder()->withSentence(new HtmlString(base64_decode('2KrYtdmF2YrZhSDZiCDYqti32YjZitixINio2YjYp9iz2LfYqSA8YSBjbGFzcz0idGV4dC1wcmltYXJ5LTUwMCIgaHJlZj0iaHR0cHM6Ly9tc2FpZWQuY29tLyI+2YXYrdmF2K8g2LPYudmK2K88L2E+'))),
                FilamentFullCalendarPlugin::make()
                    ->selectable(),
                SettingsPlugin::make()
                    ->pages([
                        SystemSettings::class,
                    ]),
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
                ApplyActiveScope::class,
                ApplyUserCaseTypeScope::class,
            ], true);
    }
}
