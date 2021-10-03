<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Factory;
use Modules\DD;

class CreateNewDbConnectionController extends Controller
{
    public function handle()
    {
        $factory = new Factory();

        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });

        $connection = $factory->connect([
            'type' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'rz_framework',
            'username' => 'root',
            'password' => 'root',
        ]);

        $users = $connection->query()
            ->select()
            ->from('users')
            ->all();

        return view('db-example-query', [
            'number' => 42,
            'users' => $users,
        ]);
    }
}