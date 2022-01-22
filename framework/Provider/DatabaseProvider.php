<?php

namespace Framework\Provider;

use Framework\db\Connection\MysqlConnection;
use Framework\db\Connection\SqliteConnection;
use Framework\db\Factory;
use Framework\Support\DriverFactory;
use Framework\Support\DriverProvider;

class DatabaseProvider extends DriverProvider
{
    protected function name(): string
    {
        return 'database';
    }

    protected function factory(): DriverFactory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'sqlite' => function($config) {
                return new SqliteConnection($config);
            },
            'mysql' => function($config) {
                return new MysqlConnection($config);
            },
        ];
    }
}
