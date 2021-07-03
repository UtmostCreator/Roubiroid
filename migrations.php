<?php

use app\core\Application;
use app\models\User;

/* ERROR REPORTING */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/* ERROR REPORTING */

require_once __DIR__ . './vendor/autoload.php';
$config = require_once 'common/config/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = $config = [
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
        'folder' => 'migrations',
        'table' => 'migrations'
    ],
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

    'userClass' => \app\models\User::class,
    'website' => [
        'name' => 'Your Web Site Name'
    ],
    'layout' => [
        'value' => 'main',
        'folder' => 'layouts',
    ],
    'views' => [
        'folder' => 'views'
    ],
];
$app = new Application(__DIR__, $config);

if (in_array('-up', $argv)) {
    $app->db->applyMigrations();
    exit;
}

if (in_array('-down', $argv)) {
    $app->db->dropMigrations();
    exit;
}

if ($argc <= 1) {
    echo '--------------HELP---------------' . PHP_EOL;
    echo '-up - to create migration' . PHP_EOL;
    echo '-down - to drop existing migrations' . PHP_EOL;
    echo '---------------------------------' . PHP_EOL;
}
