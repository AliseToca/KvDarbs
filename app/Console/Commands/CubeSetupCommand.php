<?php

namespace App\Console\Commands;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CubeSetupCommand extends CubeInstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cube-agency:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup project from existing one in staging.cube.lv';

    public function handle(): void
    {
        $this->configureAndSetupDatabase();
        $this->importDatabaseFromStaging();
        $this->linkStorage();
        $this->importFilesFromStaging();
        $this->generateKey();

        $this->info('Setup completed successfully!');
    }

    protected function importDatabaseFromStaging(): void
    {
        $this->info('Import staging database...');

        $import = confirm(
            label: 'Import database from staging.cube.lv?',
            default: false,
        );

        if (!$import) {
            return;
        }

        $remoteName = text(
            label: 'Enter remote database name',
            default: $this->database,
            required: true,
        );

        $this->info('Importing staging database...');

        putenv('DB_SOURCE_NAME=' . $remoteName);
        putenv('DB_LOCAL_NAME=' . $this->database);
        putenv('DB_LOCAL_USERNAME=' . $this->username);
        putenv('DB_LOCAL_PASSWORD=' . $this->password);

        shell_exec('vendor/deployer/deployer/bin/dep artisan:import-staging-database staging');
    }

    public function importFilesFromStaging(): void
    {
        $this->info('Import staging files...');

        $import = confirm(
            label: 'Import files from staging.cube.lv?',
            default: false,
        );

        if (!$import) {
            return;
        }

        $this->info('Importing staging files...');

        shell_exec('vendor/deployer/deployer/bin/dep artisan:import-staging-files staging');
    }
}
