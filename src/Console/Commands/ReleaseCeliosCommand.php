<?php

namespace Celios\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class ReleaseCeliosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'celios:release
                            {tag? : Release tag version (e.g. v1.0.5). Auto-increments patch version if omitted}
                            {--package=* : Specific package(s) to release (core, crm, payment). Releases all if omitted}
                            {--message= : Optional annotated tag message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Split monorepo packages (core, crm, payment) and push release tags to GitHub';

    /**
     * Package definitions for subtree splitting.
     *
     * @var array<string, array{prefix: string, repo: string, branch: string}>
     */
    protected array $packages = [
        'core' => [
            'prefix' => 'packages/core',
            'repo' => 'git@github.com:celiclazar/celios-core.git',
            'branch' => 'main',
        ],
        'crm' => [
            'prefix' => 'packages/crm',
            'repo' => 'git@github.com:celiclazar/celios-crm.git',
            'branch' => 'master',
        ],
        'payment' => [
            'prefix' => 'packages/payment',
            'repo' => 'git@github.com:celiclazar/celios-payment.git',
            'branch' => 'main',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! File::isDirectory(base_path('packages/core'))) {
            $this->components->error('This command can only be run inside the Celios monorepo (packages/core not found).');

            return Command::FAILURE;
        }

        $tag = $this->argument('tag') ?: $this->detectNextTag();
        if (! str_starts_with($tag, 'v')) {
            $tag = 'v'.$tag;
        }

        $selectedPackages = $this->option('package');
        $targets = empty($selectedPackages)
            ? $this->packages
            : array_intersect_key($this->packages, array_flip($selectedPackages));

        if (empty($targets)) {
            $this->components->error('No valid packages selected. Available: core, crm, payment.');

            return Command::FAILURE;
        }

        $this->components->info("Releasing Celios CMS [{$tag}]...");

        // 1. Tag and push monorepo
        $message = $this->option('message') ?: "Release {$tag}";
        $this->components->task("Tagging monorepo with {$tag}", function () use ($tag, $message) {
            $this->runShell("git tag -f -a {$tag} -m ".escapeshellarg($message));

            return $this->runShell("git push origin {$tag} --force");
        });

        // 2. Subtree split & push each package
        foreach ($targets as $name => $config) {
            if (! File::isDirectory(base_path($config['prefix']))) {
                continue;
            }

            $splitBranch = "split-{$name}";

            $this->components->task("Splitting and pushing [celios/{$name}] ({$tag})", function () use ($config, $splitBranch, $tag) {
                if (! $this->runShell("git subtree split --prefix={$config['prefix']} -b {$splitBranch}")) {
                    return false;
                }

                $pushedBranch = $this->runShell("git push {$config['repo']} {$splitBranch}:{$config['branch']} --force");
                $pushedTag = $this->runShell("git push {$config['repo']} {$splitBranch}:refs/tags/{$tag} --force");

                return $pushedBranch && $pushedTag;
            });
        }

        $this->newLine();
        $this->components->info("Release [{$tag}] published! Run 'php artisan celios:update' in celios-starter or client projects.");

        return Command::SUCCESS;
    }

    /**
     * Auto-detect the next patch version from git tags.
     */
    protected function detectNextTag(): string
    {
        $result = Process::path(base_path())->run('git tag -l "v*" --sort=-v:refname');
        $lines = array_filter( array_map('trim', explode("\n", $result->output())) );
        $latest = reset($lines) ?: 'v1.0.0';

        if (preg_match('/^v(\d+)\.(\d+)\.(\d+)$/', $latest, $matches)) {
            $nextPatch = ((int) $matches[3]) + 1;

            return "v{$matches[1]}.{$matches[2]}.{$nextPatch}";
        }

        return 'v1.0.5';
    }

    /**
     * Execute a shell command.
     */
    protected function runShell(string $command): bool
    {
        $result = Process::path(base_path())->timeout(600)->run($command);

        if (! $result->successful()) {
            $this->newLine();
            $this->components->error("Failed: {$command}");
            $this->line($result->errorOutput() ?: $result->output());

            return false;
        }

        return true;
    }
}
