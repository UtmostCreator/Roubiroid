<?php

$dotenv = Dotenv\Dotenv::createImmutable(\Framework\Paths::getBase());
$dotenv->load();
$extraPath = isDev() ? $_ENV['LOCAL_PROJECT_LOC'] : $_ENV['REMOTE_PROJECT_LOC'];
require_once $_SERVER['DOCUMENT_ROOT'] . $extraPath . 'config/consts.php';

//\Modules\DD\DD::dd(ASSET_URL);
return $config = [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */
    'default' => $_ENV['DB_CONNECTION'], // TODO

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */
    'migrations' => [
        'folder' => 'database/migrations/',
        'table' => 'migrations',
        'old_folder' => 'migrations',
        'old_table' => 'migrations'
    ],
    // TODO maybe move to migrations?
    'seeders' => [
        'folder' => 'database/seeders/',
        'table' => 'seeders',
    ],
    'userClass' => \models\User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],

    // TODO make it possible to change it
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
//            'url' => $_ENV['DATABASE_URL'],
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
//            'prefix' => '',
//            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
//            'options' => extension_loaded('pdo_mysql') ? array_filter([
//                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//            ]) : [],
        ]
    ],
    'website' => [
        'name' => 'Your Web Site Name'
    ],
    'layout' => [
        'value' => 'main',
        'folder' => 'layouts',
    ],
    'views' => [
        'folder' => 'resources/views'
    ],
];
