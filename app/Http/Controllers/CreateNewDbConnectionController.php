<?php

namespace App\Http\Controllers;

use App\Models\NewUser;
use Framework\Controller;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Connection\SqliteConnection;
use Framework\db\Factory;
use Framework\helpers\Config;
use models\Product;
use models\Profile;
use models\User;
use Modules\DD;

class CreateNewDbConnectionController extends Controller
{
    public function handle()
    {

        /* OLD way
        $factory = new Factory();

        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });

        $factory->addConnector('sqlite', function ($config) {
            return new SqliteConnection($config);
        });

        $connection = $factory->connect(Config::get('connections=default'));

        $users = $connection->query()
            ->select()
            ->from('users')
            ->all();*/
        $users = NewUser::all();

        $user = NewUser::where('id', 15);
        $user->delete();
        DD::dd($users);

        return view('db-example-query', [
            'number' => 42,
            'users' => $users,
        ]);
    }
}
