<?php

namespace Celios\Core;

use Illuminate\Support\ServiceProvider;

class CeliosServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('celios', function () {
            return new Celios();
        });

        // 1. Auto-merge Core configs
        if (file_exists(__DIR__ . '/../config/modules.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/modules.php', 'modules');
        }

        if (file_exists(__DIR__ . '/../config/locales.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/locales.php', 'locales');
        }

        if (file_exists(__DIR__ . '/../config/sitemap.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../config/sitemap.php', 'sitemap');
        }

        // 2. Load Core helpers
        if (file_exists(__DIR__ . '/Helpers/settings.php')) {
            require_once __DIR__ . '/Helpers/settings.php';
        }

        if (file_exists(__DIR__ . '/Helpers/pages.php')) {
            require_once __DIR__ . '/Helpers/pages.php';
        }

        // 3. Register Module Service Provider
        $this->app->register(\Celios\Core\Providers\ModuleServiceProvider::class);

        // 4. Register Dynamic Class Alias Autoloader for seamless App\* -> Celios\Core\* backwards-compatibility
        spl_autoload_register(function (string $class): void {
            $prefixes = [
                'App\\Providers\\' => 'Celios\\Core\\Providers\\',
                'App\\Console\\Commands\\' => 'Celios\\Core\\Console\\Commands\\',
                'App\\Models\\' => 'Celios\\Core\\Models\\',
                'App\\Filament\\' => 'Celios\\Core\\Filament\\',
                'App\\Services\\' => 'Celios\\Core\\Services\\',
                'App\\Enums\\' => 'Celios\\Core\\Enums\\',
                'App\\Observers\\' => 'Celios\\Core\\Observers\\',
                'App\\Notifications\\' => 'Celios\\Core\\Notifications\\',
                'App\\Support\\' => 'Celios\\Core\\Support\\',
                'App\\Jobs\\' => 'Celios\\Core\\Jobs\\',
                'App\\Policies\\' => 'Celios\\Core\\Policies\\',
                'App\\Http\\Controllers\\' => 'Celios\\Core\\Http\\Controllers\\',
                'App\\Http\\Middleware\\' => 'Celios\\Core\\Http\\Middleware\\',
                'App\\Http\\Requests\\Api\\' => 'Celios\\Core\\Http\\Requests\\Api\\',
                'App\\Http\\Resources\\' => 'Celios\\Core\\Http\\Resources\\',
            ];

            foreach ($prefixes as $appPrefix => $celiosPrefix) {
                if (str_starts_with($class, $appPrefix)) {
                    $targetClass = $celiosPrefix . substr($class, strlen($appPrefix));
                    if (class_exists($targetClass) || interface_exists($targetClass) || trait_exists($targetClass) || enum_exists($targetClass)) {
                        class_alias($targetClass, $class);
                    }
                    return;
                }
            }
        });

        // 4. Common Static Aliases
        if (!class_exists('App\Services\ModuleManager', false)) {
            class_alias(\Celios\Core\Services\ModuleManager::class, 'App\Services\ModuleManager');
        }
        if (!enum_exists('App\Enums\DocumentAccessLevel', false)) {
            class_alias(\Celios\Core\Enums\DocumentAccessLevel::class, 'App\Enums\DocumentAccessLevel');
        }
        if (!enum_exists('App\Enums\PageType', false)) {
            class_alias(\Celios\Core\Enums\PageType::class, 'App\Enums\PageType');
        }
        if (!trait_exists('App\Filament\Traits\HasModuleToggle', false)) {
            class_alias(\Celios\Core\Filament\Traits\HasModuleToggle::class, 'App\Filament\Traits\HasModuleToggle');
        }
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // 1. Load Core Migrations
        if (is_dir(__DIR__ . '/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        // 2. Load Core Views
        if (is_dir(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'celios');
            $this->app['view']->addLocation(__DIR__ . '/../resources/views');

            if (is_dir(__DIR__ . '/../resources/views/vendor/filament-panels')) {
                $this->loadViewsFrom(__DIR__ . '/../resources/views/vendor/filament-panels', 'filament-panels');
            }
        }

        // 3. Load Core Translations
        if (is_dir(__DIR__ . '/../lang')) {
            $this->loadTranslationsFrom(__DIR__ . '/../lang', 'celios');
            $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');
            if (method_exists($this->app['translator']->getLoader(), 'addPath')) {
                $this->app['translator']->getLoader()->addPath(__DIR__ . '/../lang');
            }
        }

        // 4. Load Core API Routes
        if (file_exists(__DIR__ . '/../routes/api.php')) {
            \Illuminate\Support\Facades\Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../routes/api.php');
        }

        // 5. Load Core Web Routes
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(__DIR__ . '/../routes/web.php');
        }



        // 4. Publishable assets, views, translations, and configs
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Celios\Core\Console\Commands\GenerateSitemapCommand::class,
                \Celios\Core\Console\Commands\MakeModuleCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/modules.php' => config_path('modules.php'),
                __DIR__ . '/../config/locales.php' => config_path('locales.php'),
                __DIR__ . '/../config/sitemap.php' => config_path('sitemap.php'),
            ], 'celios-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/celios'),
            ], 'celios-views');

            $this->publishes([
                __DIR__ . '/../lang' => $this->app->langPath(),
            ], 'celios-lang');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'celios-migrations');
        }
    }
}
