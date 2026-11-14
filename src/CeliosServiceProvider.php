<?php

namespace Celios\Core;

use Celios\Core\Console\Commands\GenerateSitemapCommand;
use Celios\Core\Console\Commands\InstallCeliosCommand;
use Celios\Core\Console\Commands\MakeModuleCommand;
use Celios\Core\Console\Commands\ReleaseCeliosCommand;
use Celios\Core\Console\Commands\UpdateCeliosCommand;
use Celios\Core\Enums\DocumentAccessLevel;
use Celios\Core\Enums\PageType;
use Celios\Core\Filament\Traits\HasModuleToggle;
use Celios\Core\Providers\ModuleServiceProvider;
use Celios\Core\Services\ModuleManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CeliosServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('celios', function () {
            return new Celios;
        });

        // Register Celios Shield (with dynamic module discovery filter)
        $this->app->scoped('filament-shield', fn (): \Celios\Core\Services\Shield\CeliosShield => new \Celios\Core\Services\Shield\CeliosShield);


        // 1. Auto-merge Core configs
        if (file_exists(__DIR__.'/../config/modules.php')) {
            $this->mergeConfigFrom(__DIR__.'/../config/modules.php', 'modules');
        }

        if (file_exists(__DIR__.'/../config/locales.php')) {
            $this->mergeConfigFrom(__DIR__.'/../config/locales.php', 'locales');
        }

        if (file_exists(__DIR__.'/../config/sitemap.php')) {
            $this->mergeConfigFrom(__DIR__.'/../config/sitemap.php', 'sitemap');
        }

        if (file_exists(__DIR__.'/../config/security.php')) {
            $this->mergeConfigFrom(__DIR__.'/../config/security.php', 'security');
        }

        // Ensure Livewire payload nesting depth accommodates nested Filament section/column builders
        if ((int) config('livewire.payload.max_nesting_depth', 10) < 30) {
            config(['livewire.payload.max_nesting_depth' => 30]);
        }

        // Register Security Services
        $this->app->singleton(\Celios\Core\Services\Security\CaptchaService::class);
        $this->app->bind(
            \Celios\Core\Services\Security\FileScannerInterface::class,
            function () {
                $driver = config('security.scanner.driver', 'null');

                return match ($driver) {
                    'clamav' => new \Celios\Core\Services\Security\ClamAvFileScanner,
                    default => new \Celios\Core\Services\Security\NullFileScanner,
                };
            }
        );

        // Register Theme Engine Services
        $this->app->singleton(\Celios\Core\Services\ThemeManager::class);
        $this->app->singleton(\Celios\Core\Services\ThemeInstallerService::class);

        // 2. Load Core helpers
        if (file_exists(__DIR__.'/Helpers/settings.php')) {
            require_once __DIR__.'/Helpers/settings.php';
        }

        if (file_exists(__DIR__.'/Helpers/pages.php')) {
            require_once __DIR__.'/Helpers/pages.php';
        }

        if (file_exists(__DIR__.'/Helpers/security.php')) {
            require_once __DIR__.'/Helpers/security.php';
        }

        // 3. Register Module Service Provider
        $this->app->register(ModuleServiceProvider::class);

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
                    $targetClass = $celiosPrefix.substr($class, strlen($appPrefix));
                    if (class_exists($targetClass) || interface_exists($targetClass) || trait_exists($targetClass) || enum_exists($targetClass)) {
                        class_alias($targetClass, $class);
                    }

                    return;
                }
            }
        });

        // 4. Common Static Aliases
        if (! class_exists('App\Services\ModuleManager', false)) {
            class_alias(ModuleManager::class, 'App\Services\ModuleManager');
        }
        if (! enum_exists('App\Enums\DocumentAccessLevel', false)) {
            class_alias(DocumentAccessLevel::class, 'App\Enums\DocumentAccessLevel');
        }
        if (! enum_exists('App\Enums\PageType', false)) {
            class_alias(PageType::class, 'App\Enums\PageType');
        }
        if (! trait_exists('App\Filament\Traits\HasModuleToggle', false)) {
            class_alias(HasModuleToggle::class, 'App\Filament\Traits\HasModuleToggle');
        }
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // 1. Load Core Migrations
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        // 2. Load Core Views
        if (is_dir(__DIR__.'/../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'celios');
            $this->app['view']->addLocation(__DIR__.'/../resources/views');

            if (is_dir(__DIR__.'/../resources/views/vendor/filament-panels')) {
                $this->loadViewsFrom(__DIR__.'/../resources/views/vendor/filament-panels', 'filament-panels');
            }
        }

        // 2b. Boot Active Custom Theme (Prepends Theme View Path to Cascade)
        $this->app->make(\Celios\Core\Services\ThemeManager::class)->boot();

        // 3. Load Core Translations
        if (is_dir(__DIR__.'/../lang')) {
            $this->loadTranslationsFrom(__DIR__.'/../lang', 'celios');
            $this->loadJsonTranslationsFrom(__DIR__.'/../lang');
            if (method_exists($this->app['translator']->getLoader(), 'addPath')) {
                $this->app['translator']->getLoader()->addPath(__DIR__.'/../lang');
            }
        }

        // 3b. Register Core Middleware Aliases
        $router = $this->app['router'];
        $router->aliasMiddleware('module', \Celios\Core\Http\Middleware\EnsureModuleEnabled::class);
        $router->aliasMiddleware('admin.access', \Celios\Core\Http\Middleware\AdminAccess::class);
        if (class_exists(\Spatie\Permission\Middleware\RoleMiddleware::class)) {
            $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
            $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
        }

        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::componentHook(\Celios\Core\Livewire\Hooks\EnsureModuleEnabledHook::class);
        }

        // 4. Load Core API Routes
        if (file_exists(__DIR__.'/../routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../routes/api.php');
        }

        // 5. Load Core Web Routes
        if (file_exists(__DIR__.'/../routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__.'/../routes/web.php');
        }

        // 6. Superadmin Gate Authorization & Core Policies
        Gate::policy(\Spatie\Permission\Models\Role::class, \Celios\Core\Policies\RolePolicy::class);
        Gate::policy(\Celios\Core\Models\User::class, \Celios\Core\Policies\UserPolicy::class);
        Gate::policy(\Celios\Core\Models\Section::class, \Celios\Core\Policies\SectionPolicy::class);
        Gate::policy(\Celios\Core\Models\Post::class, \Celios\Core\Policies\PostPolicy::class);
        Gate::policy(\Celios\Core\Models\Page::class, \Celios\Core\Policies\PagePolicy::class);
        Gate::policy(\Celios\Core\Models\Category::class, \Celios\Core\Policies\CategoryPolicy::class);
        Gate::policy(\Celios\Core\Models\Menu::class, \Celios\Core\Policies\MenuPolicy::class);
        Gate::policy(\Celios\Core\Models\NewsletterCampaign::class, \Celios\Core\Policies\NewsletterCampaignPolicy::class);
        Gate::policy(\Celios\Core\Models\NewsletterSubscriber::class, \Celios\Core\Policies\NewsletterSubscriberPolicy::class);
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \Celios\Core\Policies\ActivityPolicy::class);

        Gate::before(function ($user, $ability) {
            if (is_object($user) && method_exists($user, 'hasRole')) {
                return ($user->hasRole('super_admin') || $user->hasRole('super-admin')) ? true : null;
            }

            return null;
        });

        // 7. Security Event Auditing Subscriber
        \Illuminate\Support\Facades\Event::subscribe(\Celios\Core\Listeners\SecurityEventSubscriber::class);

        // 8. Security Rate Limiters
        \Illuminate\Support\Facades\RateLimiter::for('api-login', function (\Illuminate\Http\Request $request) {
            $email = (string) $request->input('email');
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip().'|'.strtolower($email));
        });

        \Illuminate\Support\Facades\RateLimiter::for('form-submissions', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('newsletter-subscribe', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('passkeys-options', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->ip());
        });

        // 9. Blade Directives
        \Illuminate\Support\Facades\Blade::directive('nonce', function () {
            return '<?php echo app()->has("csp-nonce") ? \'nonce="\' . e(app("csp-nonce")) . \'"\' : ""; ?>';
        });

        // 10. Publishable assets, views, translations, and configs
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCeliosCommand::class,
                UpdateCeliosCommand::class,
                ReleaseCeliosCommand::class,
                GenerateSitemapCommand::class,
                MakeModuleCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/modules.php' => config_path('modules.php'),
                __DIR__.'/../config/locales.php' => config_path('locales.php'),
                __DIR__.'/../config/sitemap.php' => config_path('sitemap.php'),
                __DIR__.'/../config/security.php' => config_path('security.php'),
            ], 'celios-config');

            $this->publishes([
                __DIR__.'/../resources/css/filament/admin/theme.css' => resource_path('css/filament/admin/theme.css'),
            ], 'celios-theme');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/celios'),
            ], 'celios-views');

            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath(),
            ], 'celios-lang');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'celios-migrations');
        }
    }
}
