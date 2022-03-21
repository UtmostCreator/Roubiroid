<?php

namespace Framework\db\Command;

use Framework\db\Connection\Connection;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Connection\SqliteConnection;
use Framework\db\Factory;
use Framework\helpers\ArrayHelper;
use Framework\helpers\Config;
use Modules\DD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';
    protected static ?string $migrationTable = null;
    protected array $paths = [];
    protected ?Connection $connection = null;

    // POSSIBLE migration names:
    /*
     * TODO class name must match exactly as filename
     * /^(\d+)_(\d+_){4}(create|add|drop|alter|change|remove|delete|rename)_(\w+)_table$/'
     *
     * m04072021_123211_add_notification_table.php
     * m04072021_123211_create_notification_table.php
     * m04072021_123211_drop_notification_table.php
     * m04072021_123211_alter_notification_column.php
     * m04072021_123211_change_notification_date_column.php
     * m04072021_123211_remove_notification_table.php
     * m04072021_123211_delete_notification_table.php
     * m04072021_123211_rename_notification_to_new_notification_table.php
     *
     * TODO class name must match exactly as filename BUT w/o prefixed NUMBER(001 or so)
     * \d+{1,4}_(add|drop|alter|change|remove|delete|rename)(\w+)
     *
     * 001_CreateOrdersTable
     * 001_AddOrdersTable
     * 001_DropOrdersTable
     * 001_AlterOrdersTable
     * 001_ChangeOrdersTable
     * 001_RemoveColumnFromOrdersTable
     * 001_DeleteOrdersTable
     * 001_RenameOrdersTableToOrderTable
     * */

    // TODO create migration from console
    // VALIDATE if table / column was inserted
    // ask for input if you want to create new migration after u created 1
    // set silent mode -- no output
    // TODO seeds
    // validate after insertion number of records were presented and inserted

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
        DD::disableStyles();
        $migrationTableName = self::$migrationTable = Config::get('migrations.table');
        $this
            ->setDescription('Easily do migration things with Roubiroud Framework (UtmostCreator) and Symfony Command')
            ->addOption(
                'up',
                'u',
                InputOption::VALUE_NONE,
                'Creates Migration table in case it does not exist, and executes all migrations'
            )
            ->addOption(
                'down',
                'd',
                InputOption::VALUE_NONE,
                'Drops all tables from the migration table'
            )
            ->addOption(
                'drop-migration-table',
                'dt',
                InputOption::VALUE_NONE,
                "Drops the main table <{$migrationTableName}>"
            )
            ->addOption(
                'fresh',
                'f',
                InputOption::VALUE_NONE,
                'Roll back all migrations, and run it again'
            )
            ->addOption(
                'list-all',
                'la',
                InputOption::VALUE_NONE,
                'Shows executed and not executed migrations'
            )
            ->addOption(
                'list-executed',
                'le',
                InputOption::VALUE_NONE,
                'Shows executed migrations'
            )
            ->addOption(
                'list-new',
                'ln',
                InputOption::VALUE_NONE,
                'Shows newly added migrations'
            )
            ->setHelp('Using this tool, you could manage your migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO old way to be removed.
//        $current = getcwd();
//        $pattern = 'database/migrations/*.php';
//        $this->paths = glob("{$current}/{$pattern}");

//        var_dump("{$current}/{$pattern}");
//        var_dump($this->paths);
//        $this->paths = FileHelper::getAllFilesAndFolderIn(FileHelper::mergePath(
//            [basePath('', true), Config::get('migrations.folder')]
//        ));
        $this->paths = $this->scanFolder();
//        DD::dd($this->paths);
//        var_dump(Config::get());
//        var_dump($this->paths);
        // TODO must have to see other chapters [currently on chapter 7]
//        var_dump(Config::get('migrations.folder'));
//        exit;

        if (count($this->paths) < 1) {
            $output->writeln('<info>No migrations were found</info>');
            return Command::SUCCESS;
        }

        $this->connection = $this->connection();


        $isMTableExist = $this->isMigrationTableExists();
        $migrationTableName = self::$migrationTable;
        if ($input->getOption('down')) {
            if (!$isMTableExist) {
                $output->writeln(sprintf('<error>There is no <%s> table</error>', $migrationTableName));
                return Command::FAILURE;
            }
            if ($this->isMigrationTableEmpty()) {
                $output->writeln(sprintf('<comment>The <%s> table is empty</comment>', $migrationTableName));
                return Command::SUCCESS;
            }
            $output->writeln('<error>Dropping existing tables</error>');
            $this->migrateDown($output);
            return Command::SUCCESS;
        } elseif (
            ($input->getOption('fresh'))
        ) {
            $output->writeln('<info>Fresh migration is running</info>');
            $output->writeln('<comment>- Dropping existing database tables</comment>');
            $this->migrateDown($output);

            if (!$isMTableExist) {
                $this->createMigrationTable();
            }

            $this->migrateUp($input, $output, $migrationTableName);
        } elseif ($input->getOption('up')) {
            $output->writeln('<info>Migration has stared</info>');
            if (!$isMTableExist) {
                $this->createMigrationTable();
            } else {
                $output->writeln("<comment>The Migration table <{$migrationTableName}> already exists</comment>");
            }

            $this->migrateUp($input, $output, $migrationTableName);
        } elseif ($input->getOption('list-executed')) {
            $output->writeln('<info>Executed Migration List:</info>');
            $this->listExecuted($output);
            return Command::SUCCESS;
        } elseif ($input->getOption('list-all')) {
            $output->writeln('<info>Full Migration List:</info>');
            $this->listExecuted($output);
            $output->writeln('<info>Not Executed Migration List:</info>');
            $this->listNotExecuted($output);
            return Command::SUCCESS;
        } elseif ($input->getOption('list-new')) {
            $output->writeln('<info>Not Executed Migration List:</info>');
            $this->listNotExecuted($output);
            return Command::SUCCESS;
        } elseif ($input->getOption('drop-migration-table')) {
            $output->writeln("<info>Dropping migration <{$migrationTableName}> table</info>");
            $this->dropMigrationTable($output);
            return Command::SUCCESS;
        } else {
            $output->writeln('<error>Invalid Command!</error>');
            return Command::INVALID;
        }
//        foreach ($this->paths as $path) {
//            [$prefix, $file] = explode('_', $path);
//            [$class, $extension] = explode('.', $file);
//
//            require $path;
//
//            $obj = new $class();
//            $obj->migrate($this->connection);
//        }

        return Command::SUCCESS;
    }

    private function connection(): Connection
    {
        // TODO DB connection creation - move to other place
        // e.g. app('database') or DB::getConnection()
        $factory = new Factory();

        $factory->addDriver('mysql', function ($config) {
            return new MysqlConnection($config);
        });

        $factory->addDriver('sqlite', function ($config) {
            return new SqliteConnection($config);
        });

//        $config = require getcwd() . '/config/config.php';

        return $factory->connect(Config::get('connections=default'));
    }

    private function createMigrationTable(): void
    {
        $table = $this->connection->createTable(self::$migrationTable);
        $table->id('id');
        $table->string('name');
        $table->string('table_name');
        $table->dateTime('created_at');
        $table->execute();
    }

//    private function dropMigrationTables($output, $this->connection)
//    {
//        $info = $this->connection->dropTables();
//        foreach ($info['droppedList'] as $item) {
//            $output->writeln(sprintf('<error>%s</error>', $item));// <fg=white;bg=yellow>%s</>
//        }
//        $output->writeln(sprintf('<info>Complete Percentage: %s</info>', $info['completePercentage']));
//        $output->writeln(sprintf('<fg=white;bg=white>%s</>', str_repeat('===', 10)));
//    }

    private function getExecutedMigrations(): array
    {
        if (!$this->connection->hasTable(self::$migrationTable)) {
            return [];
        }
        return $this->connection->query()
            ->select(['id', 'name'])
            ->from(self::$migrationTable)
            ->orderBy('id DESC')
            ->all();
    }

    private function isMigrationTableEmpty(): bool
    {
        if (!$this->connection->hasTable(self::$migrationTable)) {
            return false;
        }
        return $this->connection->isTableEmpty(self::$migrationTable);
    }

    private function getExecutedMigrationMappedList(): array
    {
        $res = $this->getExecutedMigrations();
        return ArrayHelper::map($res);
    }

    private function listExecuted(OutputInterface $output)
    {
//        $tableList = $this->connection->getTables();
        $tableList = $this->getExecutedMigrations();
        foreach ($tableList as $migration) {
            $output->writeln(sprintf("<fg=white;bg=black>\t– %s</>", $migration['name']));
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $migrationTableName
     * @return void
     */
    protected function migrateUp(InputInterface $input, OutputInterface $output, string $migrationTableName): void
    {
        $executedMigrations = $this->getExecutedMigrationMappedList();
        foreach ($this->paths as $path) {
            list($prefix, $file, $class, $extension) = $this->getFilesInfo($path);
            require_once $path;

            if (!empty($executedMigrations) && in_array($class, $executedMigrations)) {
                $output->writeln(sprintf("<info>Migration <%s> is already up</info>", $class::$tableName));
                continue;
            }

            if (!class_exists($class)) {
                throw new \InvalidArgumentException('Class must be have an underscore! 01_ClassName.php');
            }

            $obj = new $class();

            $helper = $this->getHelper('question');
            $questionArr = [
                'text' => '',
                'msgText' => '',
                'responseText' => '',
            ];

            $hasTable = $this->connection->hasTable($class::$tableName);
            if ($hasTable) {
                if (in_array(strtolower($file), ['create', 'test'])) {
                    $questionArr['text'] = sprintf("<info>\t Do you want to remove <%s> table from the DB? (Yes/no)\t</info>", $class::$tableName);
                    $questionArr['msgText'] = sprintf("<info>\t <%s> table exists, but it was explicitly removed from <%s>!</info>", $class::$tableName, static::$migrationTable);
                    $questionArr['responseText'] = sprintf("<fg=white;bg=red>\t– <%s> table has been removed!</>", $class::$tableName);
                } else {
                    $questionArr['text'] = sprintf('Yes - remove migration called <%1$s>, No - Add column to <%1$s> (Yes/no) ' . "\t", $class);
                    $questionArr['msgText'] = '';
                    $questionArr['responseText'] = sprintf("<fg=black;bg=green>\t– modification for <%s> table called <%s> is undone</>", $class::$tableName, $class);
                }

//                DD::dd(!$this->hasRecord(static::$migrationTable, ['name'], [$class]));
                if (!$this->hasRecord(static::$migrationTable, ['name'], [$class])) {
                    if (!empty($questionArr['msgText'])) {
                        $output->writeln($questionArr['msgText']);
                    }
                    $question = new Question($questionArr['text'], true);
                    $answer = $helper->ask($input, $output, $question);

                    if (in_array($answer, [true, 'yes', 'y', 1], true)) {
                        $obj->down($this->connection);
                        $output->writeln($questionArr['responseText']);
                        continue;
                    }
                }
            }
            // TODO check if table is not already inserted
            $obj->up($this->connection);

            $this->insertExecutedMigrationIntoTable($migrationTableName, $class);

            if ($class::$column) {
                $output->writeln(sprintf("<fg=black;bg=green>\t– <%s> column is added!</>", $class::$tableName));
            } else {
                $output->writeln(sprintf("<fg=black;bg=green>\t– <%s> table is created!</>", $class::$tableName));
            }
        }
    }

    /**
     * @param
     * @return void
     */
    protected function migrateDown(OutputInterface $output): void
    {
        $executedMigrations = $this->getExecutedMigrationMappedList();
//        DD::dd($this->paths);
        foreach ($this->paths as $path) {
            [$prefix, $file] = explode('_', $path);
            [$class, $extension] = explode('.', $file);
            require_once $path;

            if (empty($executedMigrations) || !in_array($class, $executedMigrations)) {
                continue;
            }

            if (!class_exists($class)) {
                throw new \InvalidArgumentException('Class must be have an underscore! 01_ClassName.php');
            }

            $obj = new $class();
            // TODO check if table is not already inserted
            $obj->down($this->connection);

            if ($class::$column) {
                $output->writeln(sprintf("<fg=white;bg=red>\t– <%s> column is removed!</>", $class::$tableName));
            } else {
                $output->writeln(sprintf("<fg=white;bg=red>\t– <%s> table is dropped!</>", $class::$tableName));
            }
            $this->connection->query()->from(self::$migrationTable)->where('name', '=', $class)->delete();
        }
    }

    private function listNotExecuted(OutputInterface $output)
    {
//        DD::dd($this->paths);
        $new = [];
        foreach ($this->paths as $path) {
            list($prefix, $file, $class, $extension) = $this->getFilesInfo($path);
//            require_once $path;
              $new[] = $class;
        }
//        exit;
        $notExecuted = ArrayHelper::getArrayFullDifferenceByValue($this->getExecutedMigrationMappedList(), $new);
        foreach($notExecuted as $m) {
            $output->writeln(sprintf("<fg=white;bg=black>\t– %s</>", $m));
        }
    }

    private function dropMigrationTable(OutputInterface $output)
    {
        $this->connection->dropTable(static::$migrationTable);
        $output->writeln(sprintf("<fg=red;bg=white>\t– %s is dropped</>", static::$migrationTable));
    }

    /**
     * @param string $migrationTableName
     * @param $class
     * @return void
     */
    protected function insertExecutedMigrationIntoTable(string $migrationTableName, $class): void
    {
        $this->connection
            ->query()
            ->to($migrationTableName)
            ->insert(['name', 'table_name', 'created_at'], [
                'name' => $class,
                'table_name' => $class::$tableName,
                'created_at' => date('Y-m-d H:m:i')]);
    }

    private function scanFolder(): array
    {
        $filePattern = '*.php';
        $migrationsPaths = glob(join('', [basePath('', true), Config::get('migrations.folder'), '', $filePattern]));
//        $seedersPaths = glob(join('', [basePath('', true), Config::get('seeders.folder'), '', $filePattern]));
//        $paths = array_merge($migrationsPaths, $seedersPaths);
        $paths = $migrationsPaths;
        return $paths;
    }

    private function isMigrationTableExists(): bool
    {
        return $this->connection->hasTable(self::$migrationTable);
    }

    /**
     * @param $path
     * @return array
     */
    protected function getFilesInfo($path)
    {
        // regexp to extract it: https://regexr.com/6eqm7
        // REGEXP:
        /* find the last DIRECTORY_SEPARATOR
         * get file with ext;
         * get only filename
         * get only ext
         * get only file number|date
         *
        */

        $findMatches = function ($path) {
            $matches = [];
            $separator = str_repeat(DIRECTORY_SEPARATOR, 2);
            preg_match(sprintf('#(.*)%1$s(?:(?!%1$s))(([0-9]+)_(\w+)\.([a-zA-Z]+))#', $separator), $path, $matches);
            return $matches;
        };
        $matches = $findMatches($path);
        list(, $pathToFile, $FQN, $number, $class, $ext) = $matches;
//        DD::dd($pathToFile);
        [$prefix, $file] = explode('_', $path);
        [$class, $extension] = explode('.', $file);
        return [$prefix, $file, $class, $extension];
    }

    private function showIncorrectlyNameMigrations()
    {
        // TODO shows incorrectly named migrations that do not match the regular exp
    }

    private function hasRecord($table, array $columns, array $values): bool
    {
        return $this->connection->query()->from(static::$migrationTable)->isRecordExist($table, $columns, $values);
    }
}
