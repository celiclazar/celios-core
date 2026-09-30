<?php

namespace Celios\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'celios:make-module {name : The name of the module, e.g. Ecommerce, Booking, Shop} {--path=packages : Target destination (packages or Modules)}';

    /**
     * The console command description.
     */
    protected $description = 'Scaffold a new modular, package-ready module for Celios CMS';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rawName = $this->argument('name');
        $studlyName = Str::studly($rawName);
        $kebabName = Str::kebab($rawName);
        $snakeName = Str::snake($rawName);

        $modulePath = $this->option('path') === 'Modules'
            ? base_path("Modules/{$studlyName}")
            : base_path("packages/{$kebabName}");

        if (File::exists($modulePath)) {
            $this->error("Module [{$studlyName}] already exists at {$modulePath}!");
            return self::FAILURE;
        }

        $directories = [
            $modulePath . '/Config',
            $modulePath . '/Database/Migrations',
            $modulePath . '/Filament/Resources',
            $modulePath . '/Filament/Pages',
            $modulePath . '/Http/Controllers',
            $modulePath . '/Models',
            $modulePath . '/Resources/Views',
            $modulePath . '/Routes',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        // 1. composer.json
        $composerJson = json_encode([
            'name' => "celios/{$kebabName}",
            'description' => "{$studlyName} module for Celios CMS",
            'type' => 'library',
            'license' => 'MIT',
            'require' => [
                'php' => '^8.3',
                'illuminate/support' => '^11.0|^12.0|^13.0',
            ],
            'autoload' => [
                'psr-4' => [
                    "Modules\\{$studlyName}\\" => '',
                ],
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        "Modules\\{$studlyName}\\{$studlyName}ServiceProvider",
                    ],
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        File::put($modulePath . '/composer.json', $composerJson . PHP_EOL);

        // 2. Service Provider
        $serviceProviderStub = <<<PHP
<?php

namespace Modules\\{$studlyName};

use Illuminate\Support\ServiceProvider;

class {$studlyName}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->mergeConfigFrom(__DIR__.'/Config/{$snakeName}.php', '{$snakeName}');
    }

    public function boot(): void
    {
        \$this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (file_exists(__DIR__.'/Routes/web.php')) {
            \$this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        }

        if (file_exists(__DIR__.'/Routes/api.php')) {
            \$this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        }

        \$this->loadViewsFrom(__DIR__.'/Resources/Views', '{$kebabName}');
    }
}
PHP;
        File::put($modulePath . "/{$studlyName}ServiceProvider.php", $serviceProviderStub . PHP_EOL);

        // 3. Config
        $configStub = <<<PHP
<?php

return [
    'name' => '{$studlyName}',
    'enabled' => true,
];
PHP;
        File::put($modulePath . "/Config/{$snakeName}.php", $configStub . PHP_EOL);

        // 4. Routes
        File::put($modulePath . '/Routes/web.php', "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Module web routes\n");
        File::put($modulePath . '/Routes/api.php', "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Module API routes\n");

        $this->info("Module [{$studlyName}] successfully created at [{$modulePath}]!");
        $this->comment("Next steps:");
        $this->comment("1. Add '\"Modules\\\\{$studlyName}\\\\\": \"packages/{$kebabName}/\"' to root composer.json autoload.psr-4 if local");
        $this->comment("2. Register 'Modules\\{$studlyName}\\{$studlyName}ServiceProvider::class' in config/modules.php or bootstrap/providers.php");
        $this->comment("3. Run: composer dump-autoload");

        return self::SUCCESS;
    }
}
