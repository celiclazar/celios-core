<?php

namespace Celios\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class UpdateCeliosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'celios:update
                            {--production : Run full production deployment pipeline (git pull, composer install, migrate, build, optimize)}
                            {--git : Pull latest changes from Git before updating}
                            {--lock : Install from composer.lock (composer install) instead of updating celios/* packages}
                            {--publish : Force re-publish Celios config and admin theme files}
                            {--build : Compile frontend assets (npm run build)}
                            {--optimize : Rebuild Laravel and Filament production caches after clearing}
                            {--commit : Automatically git commit and push updated composer.lock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Celios CMS packages, run migrations, clear/rebuild Filament & Laravel caches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isProduction = (bool) $this->option('production');
        $shouldGitPull = $isProduction || (bool) $this->option('git');
        $useLock = $isProduction || (bool) $this->option('lock');
        $shouldPublish = (bool) $this->option('publish');
        $shouldBuild = $isProduction || (bool) $this->option('build');
        $shouldOptimize = $isProduction || (bool) $this->option('optimize');
        $shouldCommit = (bool) $this->option('commit');

        $this->components->info('Starting Celios CMS Update Pipeline...');

        if ($isProduction) {
            $this->call('down');
        }

        try {
            // 1. Optional Git Pull
            if ($shouldGitPull) {
                $this->components->task('Pulling latest changes from Git', function () {
                    return $this->runShellCommand('git pull');
                });
            }

            // 2. Composer Update or Install
            if ($useLock) {
                $composerCmd = $isProduction
                    ? 'composer install --no-dev --optimize-autoloader --no-interaction'
                    : 'composer install --no-interaction';

                $this->components->task('Installing Composer dependencies from lockfile', function () use ($composerCmd) {
                    return $this->runShellCommand($composerCmd);
                });
            } else {
                $this->components->task('Updating celios/* packages via Composer', function () {
                    return $this->runShellCommand('composer update "celios/*" --no-interaction');
                });
            }

            // 3. Optional Config & Theme Publishing
            if ($shouldPublish) {
                $this->components->task('Publishing Celios configuration & theme assets', function () {
                    $this->callSilent('vendor:publish', ['--tag' => 'celios-config', '--force' => true]);
                    $this->callSilent('vendor:publish', ['--tag' => 'celios-theme', '--force' => true]);

                    return true;
                });
            }

            // 4. Database Migrations
            $this->components->task('Running database migrations', function () {
                $this->callSilent('migrate', ['--force' => true]);

                return true;
            });

            // 5. Clear Laravel & Filament Caches (prevents stale Filament resource errors)
            $this->components->task('Clearing Laravel and Filament caches', function () {
                $this->callSilent('optimize:clear');
                $this->callSilent('filament:optimize-clear');

                return true;
            });

            // 6. Optional Frontend Build
            if ($shouldBuild) {
                $this->components->task('Building frontend assets (npm run build)', function () {
                    return $this->runShellCommand('npm run build');
                });
            }

            // 7. Optional Production Optimization
            if ($shouldOptimize) {
                $this->components->task('Optimizing Laravel and Filament for production', function () {
                    $this->callSilent('optimize');
                    $this->callSilent('filament:optimize');
                    $this->callSilent('queue:restart');

                    return true;
                });
            }

            // 8. Optional Git Commit & Push (for starter / local repos)
            if ($shouldCommit) {
                $this->components->task('Committing and pushing updated lockfile to Git', function () {
                    return $this->runShellCommand('git add composer.lock config/ resources/ && git diff --cached --quiet || (git commit -m "chore(deps): update celios packages" && git push)');
                });
            }
        } finally {
            if ($isProduction) {
                $this->call('up');
            }
        }

        $this->newLine();
        $this->components->info('Celios CMS updated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Run a shell command in the project root directory.
     */
    protected function runShellCommand(string $command): bool
    {
        $result = Process::path(base_path())->timeout(600)->run($command);

        if (! $result->successful()) {
            $this->newLine();
            $this->components->error("Command failed: {$command}");
            $this->line($result->errorOutput() ?: $result->output());

            return false;
        }

        return true;
    }
}
