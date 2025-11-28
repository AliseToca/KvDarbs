<?php

namespace App\Console\Commands;

use Database\Seeders\CubeInstallSeeder;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CubeInstallCommand extends Command
{
    const DEFAULT_HOST = '127.0.0.1';

    const DEFAULT_PORT = 3306;

    const DEFAULT_USERNAME = 'root';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cube-agency:install';

    protected bool $dev = false;

    protected string $database;

    protected string $host = self::DEFAULT_HOST;

    protected int $port = self::DEFAULT_PORT;

    protected string $username = self::DEFAULT_USERNAME;

    protected ?string $password;

    protected bool $createSuperAdmin = false;

    protected Filesystem $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    public function handle(): void
    {
        $this->laravel['env'] = 'local';

        $this->dev = confirm(
            label: 'Are you developing a new application?',
            default: false,
            yes: 'No',
            no: 'Yes',
        );

        $this->configureAndSetupDatabase();
        $this->generateKey();
        $this->linkStorage();
        $this->runMigrations();
        $this->runSeeder();
        $this->loadTranslations();
        $this->publishPermissionsAndPolicies();
        $this->checkSuperAdminExists();

        if ($this->createSuperAdmin) {
            $this->createAdminUser();
        }

        $this->gitInit();

        $this->info('Installation completed successfully!');
    }

    protected function configureAndSetupDatabase(): void
    {
        $this->setDatabaseData();

        $createDatabase = confirm(
            label: 'Create new database "' . $this->database . '"?',
            default: false,
        );

        // Set DB config
        config([
            'database.connections.mysql.database' => '',
            'database.connections.mysql.host' => $this->host,
            'database.connections.mysql.port' => $this->port,
            'database.connections.mysql.username' => $this->username,
            'database.connections.mysql.password' => $this->password
        ]);

        // Force the new login to be used
        DB::purge();

        if ($createDatabase) {
            $this->createDatabase();
        }

        $this->testConnection();

        $this->setEnvironmentValue([
            'DB_HOST' => $this->host,
            'DB_PORT' => $this->port,
            'DB_DATABASE' => $this->database,
            'DB_USERNAME' => $this->username,
            'DB_PASSWORD' => $this->password,
        ]);
    }

    protected function setDatabaseData(): void
    {
        $this->info('Initializing database setup...');

        $this->database = text(
            label: 'Enter a database name',
            default: $this->guessDatabaseName(),
            required: true,
        );

        $this->host = text(
            label: 'What is your MySQL host?',
            default: $this->host,
            required: true,
        );

        $this->port = text(
            label: 'What is your MySQL port?',
            default: $this->port,
            required: true,
        );

        $this->username = text(
            label: 'What is your MySQL username?',
            default: $this->username,
            required: true,
        );

        $this->password = password(
            label: 'What is your MySQL password?',
        );
    }

    protected function guessDatabaseName(): string
    {
        try {
            $segments = array_reverse(explode(DIRECTORY_SEPARATOR, app_path()));
            $name = explode('.', $segments[1])[0];

            return Str::slug($name);
        } catch (Exception $exception) {
            return '';
        }
    }

    protected function createDatabase(): void
    {
        $this->info('Creating database...');

        DB::unprepared('CREATE DATABASE IF NOT EXISTS `' . $this->database . '` COLLATE `utf8mb4_general_ci`');
    }

    protected function testConnection(): void
    {
        try {
            DB::unprepared('USE `' . $this->database . '`');
            DB::connection()->setDatabaseName($this->database);
        } catch (Exception $e) {
            $this->error('Database "' . $this->database . '" does not exist!');
            die;
        }
    }

    protected function setEnvironmentValue(array $values): void
    {
        $content = $this->getEnvironmentFile();

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                // In case the searched variable is in the last line without \n
                $content .= PHP_EOL;
                $keyPosition = strpos($content, "{$envKey}=");
                $endOfLinePosition = strpos($content, PHP_EOL, $keyPosition);
                $oldLine = substr($content, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $content .= "{$envKey}={$envValue}" . PHP_EOL;
                } else {
                    $content = str_replace($oldLine, "{$envKey}={$envValue}", $content);
                }
            }
        }

        $content = substr($content, 0, -1);
        $this->filesystem->put('.env', $content);
    }

    protected function getEnvironmentFile(): string
    {
        return $this->filesystem->exists('.env')
            ? $this->filesystem->get('.env')
            : $this->filesystem->get('.env.example');
    }

    protected function generateKey(): void
    {
        $this->info('Generating application key...');
        $this->call('key:generate');
    }

    protected function linkStorage(): void
    {
        $this->info('Linking storage directory...');
        $this->call('storage:link');
    }

    protected function runMigrations(): void
    {
        $this->info('Executing database migrations...');
        $this->call('migrate');
    }

    protected function runSeeder(): void
    {
        $this->info('Executing database seeder...');
        $this->call('db:seed', [
            '--class' => CubeInstallSeeder::class
        ]);
    }

    protected function loadTranslations(): void
    {
        $this->info('Loading translations...');
        $this->call('translator:load');
        $this->call('translator:flush');
    }

    protected function publishPermissionsAndPolicies(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'filament-shield-config',
            '--force' => null,
        ]);

        $this->info('Generating permissions and policies for all entities...');

        $this->call('shield:generate', [
            '--all' => true,
        ]);
    }

    protected function createAdminUser(): void
    {
        $this->info('Creating super admin...');

        $userModel = Filament::auth()->getProvider()->getModel();

        $name = text(
            label: 'Name',
            required: true,
        );
        $email = text(
            label: 'Email address',
            required: true,
            validate: fn(string $email): ?string => match (true) {
                !filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                $userModel::where('email', $email)->exists() => 'A user with this email address already exists',
                default => null,
            },
        );
        $password = password(
            label: 'Password',
            required: true,
        );

        $this->call('make:filament-user', [
            '--name' => $name,
            '--email' => $email,
            '--password' => $password,
        ]);

        $user = $userModel::where('email', $email)->first();

        if ($user) {
            $this->setSuperAdminRole($user);
        }
    }

    protected function checkSuperAdminExists(): void
    {
        $superAdminRoleName = config('filament-shield.super_admin.name');
        $userModel = config('filament-shield.auth_provider_model.fqcn');

        $superAdminExists = Role::where('name', $superAdminRoleName)->exists()
            && $userModel::role($superAdminRoleName)->exists();

        if (!$superAdminExists) {
            $this->createSuperAdmin = true;
            return;
        }

        $this->createSuperAdmin = confirm(
            label: 'A super admin already exists. Would you like to create another super admin?',
            default: false,
        );
    }

    protected function setSuperAdminRole(Model $user): void
    {
        $this->info('Assigning super admin role to ' . $user->name . '...');

        $this->call('shield:super-admin', [
            '--user' => $user->id,
        ]);
    }

    protected function gitInit(): void
    {
        if ($this->dev) {
            return;
        }

        $this->info('Removing a boilerplate\'s repository');
        shell_exec('rm -rf ' . base_path('.git'));

        if (!`which git`) {
            $this->comment('Git not found. You should prepare and upload your repository manually.');
            return;
        }

        $this->info('Initializing a new repository');
        shell_exec('git init');
        shell_exec('git branch -m main master');

        $repositoryName = text(label: 'Enter your new repository (cube/new-project-name)');

        if (!$repositoryName) {
            $this->info('You should prepare and upload your repository manually');
            return;
        }

        shell_exec('git remote add origin git@' . config('app.git_repository.host') . ':' . $repositoryName . '.git');

        $createInitCommit = confirm(label: 'Create an initial commit?', default: true);

        if ($createInitCommit) {
            shell_exec('git add .');
            shell_exec('git commit -m "Filament boilerplate"');

            $uploadRepository = confirm(label: 'Upload your new repository?', default: true);

            if ($uploadRepository) {
                shell_exec('git push -u origin master');
            }
        }
    }
}
