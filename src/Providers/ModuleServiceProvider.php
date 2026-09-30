<?php

namespace Celios\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    protected function getModulePaths(): array
    {
        $paths = [];
        foreach ([base_path('packages'), base_path('Modules')] as $parent) {
            if (File::exists($parent)) {
                foreach (File::directories($parent) as $dir) {
                    if (basename($dir) === 'core') continue;
                    $paths[] = $dir;
                }
            }
        }
        return $paths;
    }

    public function boot(): void
    {
        $modules = $this->getModulePaths();

        foreach ($modules as $modulePath) {
            $studlyName = \Illuminate\Support\Str::studly(basename($modulePath));

            // 1. Web Routes
            if (File::exists($modulePath . '/Routes/web.php')) {
                Route::middleware('web')
                    ->group($modulePath . '/Routes/web.php');
            }

            // 2. API Routes
            if (File::exists($modulePath . '/Routes/api.php')) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($modulePath . '/Routes/api.php');
            }

            // 3. Views
            if (File::exists($modulePath . '/Resources/Views')) {
                $this->loadViewsFrom($modulePath . '/Resources/Views', $studlyName);
            }

            // 4. Migrations
            if (File::exists($modulePath . '/Database/Migrations')) {
                $this->loadMigrationsFrom($modulePath . '/Database/Migrations');
            }

            // 5. Translations
            if (File::exists($modulePath . '/Resources/lang')) {
                $this->loadTranslationsFrom($modulePath . '/Resources/lang', $studlyName);
            } elseif (File::exists($modulePath . '/lang')) {
                $this->loadTranslationsFrom($modulePath . '/lang', $studlyName);
            }
        }
    }

    public function register(): void
    {
        $modules = $this->getModulePaths();

        foreach ($modules as $modulePath) {
            $studlyName = \Illuminate\Support\Str::studly(basename($modulePath));

            // 1. Auto-register Module ServiceProvider if it exists
            $providerClass = "Modules\\{$studlyName}\\{$studlyName}ServiceProvider";
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }

            // 2. Auto-merge Module configs
            $configPath = $modulePath . '/Config';
            if (File::exists($configPath)) {
                foreach (File::files($configPath) as $configFile) {
                    $configName = pathinfo($configFile->getFilename(), PATHINFO_FILENAME);
                    $this->mergeConfigFrom($configFile->getRealPath(), $configName);
                }
            }
        }
    }
}
