<?php

namespace App\Http\Controllers;

use App\Models\NewUser;
use Framework\Controller;
use models\Product;
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

        $products = Product::all();
//        DD::dd($products);
//        dd($products);
        $productsWithRoutes = array_map(function ($product) {
            $product->route = $this->router->route('view-product', ['product' => $product->id]);
            return $product;
        }, $products);
//        DD::dd($productsWithRoutes);


        $users = NewUser::all();

        $user = NewUser::where('id', 15);
        $user->delete();
//        DD::dd($users);

        return view('db-example-query', [
            'number' => 42,
            'users' => $users,
            'products' => $products,
        ]);
    }
}
