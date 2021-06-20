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

$config = [
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
    'migrations' => [
        'folder' => 'migrations'
    ],
    'userClass' => User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
$app = new Application(__DIR__, $config);
$app->db->applyMigrations();
