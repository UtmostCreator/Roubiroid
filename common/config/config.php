<?php

require_once \app\core\PointTo::to('common/config/', 'consts.php');
$dotenv = Dotenv\Dotenv::createImmutable(dirname(dirname(__DIR__)));
$dotenv->load();

return $config = [
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
    'userClass' => \app\models\User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
