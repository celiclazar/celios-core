<?php

namespace Celios\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class InstallCeliosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'celios:install
                            {--force : Overwrite existing configuration and theme files}
                            {--migrate : Automatically run migrations without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Celios CMS core configuration, admin theme, migrations, and superadmin setup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Installing Celios CMS Core Platform...');

        // 1. Publish Configuration
        $this->components->task('Publishing Celios configuration', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'celios-config',
                '--force' => (bool) $this->option('force'),
            ]);
            return true;
        });

        // 2. Publish Admin Theme CSS
        $this->components->task('Publishing Filament Admin theme CSS', function () {
            $this->callSilent('vendor:publish', [
                '--tag' => 'celios-theme',
                '--force' => (bool) $this->option('force'),
            ]);
            return true;
        });

        // 3. Storage Symlink
        $this->components->task('Ensuring storage symlink exists', function () {
            $this->callSilent('storage:link');
            return true;
        });

        // 4. Run Migrations
        $shouldMigrate = $this->option('migrate') || (! $this->input->isInteractive()) || $this->confirm('Would you like to run database migrations now?', true);

        if ($shouldMigrate) {
            $this->components->task('Running migrations', function () {
                $this->call('migrate', ['--force' => true]);
                return true;
            });
        }

        // 5. Create Superadmin User
        if ($this->input->isInteractive()) {
            if ($this->confirm('Would you like to create a Superadmin user now?', true)) {
                $this->createSuperAdminUser();
            }
        }

        $this->newLine();
        $this->components->info('Celios CMS installation complete! Access your admin panel at: ' . url('/admin'));

        return Command::SUCCESS;
    }

    /**
     * Prompt and create a Superadmin user.
     */
    protected function createSuperAdminUser(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
            $this->components->warn('Database tables not ready yet. Please run php artisan migrate first.');
            return;
        }

        $name = $this->ask('Superadmin Name', 'Admin');
        $email = $this->ask('Superadmin Email', 'admin@example.com');
        $username = $this->ask('Superadmin Username', 'superadmin');
        $password = $this->secret('Superadmin Password');

        if (empty($password)) {
            $this->components->error('Password cannot be empty. Superadmin creation skipped.');
            return;
        }

        // Resolve user model
        $userModel = config('auth.providers.users.model', \Celios\Core\Models\User::class);

        /** @var \Celios\Core\Models\User $user */
        $user = $userModel::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->status = 'active';
        $user->save();

        // Assign super_admin role
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

        if (! $user->hasRole('super_admin')) {
            $user->assignRole($role);
        }

        $this->components->info("Superadmin user [{$email}] created successfully!");
    }
}
