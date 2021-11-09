<?php

namespace Framework\db\Command;

use Framework\db\Connection\Connection;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Connection\SqliteConnection;
use Framework\db\Factory;
use Framework\helpers\Config;
use Framework\helpers\FileHelper;
use Modules\DD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    // TODO not used
    public const CREATE_PATTERNS = [
        '/^(\d+)_(\d+_){4}create_(\w+)_table$/',
        '/^create_(\w+)$/',
    ];

    // TODO not used
    public const CHANGE_PATTERNS = [
        '/^(\d+)_(\d+_){4}(add|drop|alter|change|remove|delete|rename)_(\w+)_table$/',
    ];

    protected function configure(): void
    {
        $this
            ->setDescription('Migrates the database')
            ->addOption(
                'fresh',
                null,
                InputOption::VALUE_NONE,
                'Delete all tables before running the migrations'
            )
            ->setHelp('This command looks for all migration files and runs them');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO old way to be removed.
//        $current = getcwd();
//        $pattern = 'database/migrations/*.php';
//        $paths = glob("{$current}/{$pattern}");

//        var_dump("{$current}/{$pattern}");
//        var_dump($paths);
//        $paths = FileHelper::getAllFilesAndFolderIn(FileHelper::mergePath(
//            [base_path('', true), Config::get('migrations.folder')]
//        ));
        $paths = [];
        $filePattern = '*.php';
        $migrationsPaths = glob(join('', [base_path('', true), Config::get('migrations.folder'), '', $filePattern]));
        $seedersPaths = glob(join('', [base_path('', true), Config::get('seeders.folder'), '', $filePattern]));
        $paths = array_merge($migrationsPaths, $seedersPaths);
//        DD::dd($paths);
//        var_dump(Config::get());
//        var_dump($paths);
        // TODO must have to see other chapters [currently on chapter 7]
//        var_dump(Config::get('migrations.folder'));
//        exit;

        if (count($paths) < 1) {
            $output->writeln('No migrations found');
            return Command::SUCCESS;
        }

        $connection = $this->connection();

        if ($input->getOption('fresh')) {
            $output->writeln('Dropping existing database tables');

            $connection->dropTables();
            $connection = $this->connection();
        }

        if (!$connection->hasTable(Config::get('migrations.table'))) {
            $output->writeln('Creating migrations table');
            $this->createMigrationTable($connection);
        }

        foreach ($paths as $path) {
            [$prefix, $file] = explode('_', $path);
            [$class, $extension] = explode('.', $file);

            require $path;

            if (!class_exists($class)) {
                throw new \InvalidArgumentException('Class must be have an underscore! 01_ClassName.php');
            }

            $obj = new $class();
            // TODO check if table is not already inserted
            $obj->migrate($connection);

            $connection
                ->query()
                ->to(Config::get('migrations.table'))
                ->insert(['name', 'created_at'], ['name' => $class, 'created_at' => 'NOW()']);
        }
//        foreach ($paths as $path) {
//            [$prefix, $file] = explode('_', $path);
//            [$class, $extension] = explode('.', $file);
//
//            require $path;
//
//            $obj = new $class();
//            $obj->migrate($connection);
//        }

        return Command::SUCCESS;
    }

    private function connection(): Connection
    {
        // TODO DB connection creation - move to other place
        $factory = new Factory();

        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });

        $factory->addConnector('sqlite', function ($config) {
            return new SqliteConnection($config);
        });

//        $config = require getcwd() . '/config/config.php';

        return $factory->connect(Config::get('connections=default'));
    }

    private function createMigrationTable(Connection $connection): void
    {
        $table = $connection->createTable(Config::get('migrations.table'));
        $table->id('id');
        $table->string('name');
        $table->dateTime('created_at');
        $table->execute();
    }
}
