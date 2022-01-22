<?php

namespace App\Http\Controllers;

use App\Models\Migrations;
use App\Models\NewUser;
use Framework\Controller;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Query;
use Framework\Request;
use Framework\routing\Router;
use Framework\URL;
use models\Product;
use models\Profile;
use models\User;
use Modules\DD;

class ExampleController extends Controller
{

    public function showMigrations()
    {
        $migrations = Migrations::all();

        return view('migrations', [
            'migrations' => $migrations,
        ]);
    }

    public function insertLoop()
    {
//        $i = rand(100, 1000);
//        $i = rand(1, 5);
//        while ($i--) {
        $i = 1;
        $prod = new Product();
        $genRndStr = function ($length = 50) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        };
        $prod->name = $genRndStr();
        $prod->description = $genRndStr();
        $prod->save();
        echo 'inserted';
//        }
    }

    public function addNewUser()
    {
        $data = ['name' => 'Ім`я Користувача', 'email' => 'useremail@gmail.com', 'password' => 'AnyPassword000'];
        $user = new User();
        $user->load($data);
        if ($user->validate()) {
            $user->password = password_hash($user->password, \PASSWORD_DEFAULT);
            $user->save();
        }
    }

    public function findUserById()
    {
        $getData = Router::getActiveRoute()->parameters();
        $id = $getData['id'] ?? null;
        if (is_null($id)) {
            redirect('back');
        }
        $user = User::find($id);
        echo $user->name;
    }

    public function updateUserById()
    {
        $getData = Router::getActiveRoute()->parameters();
        $id = $getData['id'] ?? null;
        $user = User::find($id);
        $user->email = 'updatedemail@gmail.com';
        if ($user->validate()) {
            $user->save();
        } else {
            redirect('home');
        }
    }

    public function insertWOObject()
    {
//        $i = rand(100, 1000);
//        $i = rand(1, 5);
//        $i = 1000;
        $i = 1;
        $genRndStr = function ($length = 50) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        };
        while ($i--) {
            Product::query()->insert(['name', 'description'], [$genRndStr(), $genRndStr()]);
//        echo 'inserted';
        }
    }

    public function insertWOObjectTime()
    {
//        $i = rand(100, 1000);
//        $i = rand(1, 5);
//        $i = 1;
        DD::startTimeTracking();
        $genRndStr = function ($length = 50) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        };
        $i = 10000;
        while ($i--) {
            Product::query()->insert(['name', 'description'], [$genRndStr(), $genRndStr()]);
//            Product::query();
        }
//        echo 'inserted';
        DD::dd(DD::getEndResultTime());
    }

    public function oldInsert()
    {
//        $i = rand(100, 1000);
//        $i = rand(1, 5);
//        $i = 1;
        DD::startTimeTracking();
        $genRndStr = function ($length = 50) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        };
        $i = 10000;
        while ($i--) {
            Query::getInst()->insert('products', ['name', 'description'], [$genRndStr(), $genRndStr()]);
        }
//        echo 'inserted';
        DD::dd(DD::getEndResultTime());
    }

    public function selectTest()
    {
        $p1 = Product::first();
//        echo $p1->name;
//        echo $p1->description;
//        DD::dd($p1);
    }


    public function selectTestTime()
    {
        DD::startTimeTracking();
//        $i = 100000; // "7.327 sec;"
        $i = 1000;
//        DD::dd(Product::first());
        while ($i--) {
            $p1 = Product::first();
//            echo $p1->name;
//            echo $p1->description;
            unset($p1);
        }
        DD::dd(DD::getEndResultTime());
//        DD::dd($p1);
    }

    public function clearLoadTestTable()
    {
//        Product::delete();
        DD::dd(Product::query()->delete());
    }

    public function count()
    {
//        Product::delete();
        $rec = Product::query()->select('count(id) as count')->first();
        $count = $rec->count;
        DD::dd($count);
    }

    public function getSingleProductObject()
    {
        $p1 = Product::where('id', 1)->first();
        $p1->name = 'test update 2';
        $p1->update();
        DD::dd(Product::where('id', 1)->first());
    }

    public function getArrayOfProductObjects()
    {
        DD::dd(Product::all());
    }

    public function updateTheProduct()
    {
        $p1 = Product::where('id', 1)->first();
        $p1->name = 'test update 2';
        $p1->description = 'updated description of product';
        $p1->update();
        DD::dd($p1);
        DD::dd(Product::all());
    }

    public function insertTheProduct()
    {
        $newProd = new Product();
        $newProd->name = 'saved name';
        $newProd->description = 'saved desc';
        $newProd->save();
        DD::dd($newProd);
    }

    public function simpleRelationsShipManagement()
    {
        $user = new NewUser();
        $user->email = "cgpitt@gmail.com";
        $user->firstname = "some new user";
        $user->lastname = "some new user";
        $user->status = 1;
        $user->created_at = "NOW()";
        $user->password = "some new user";
        $user->save();

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->save();

        // you could get the profile from user in 2 ways:
        // 1st way using magical method
        DD::dd($user->profile);
        // 2nd way using Relationship class directly
        DD::dd($user->profile()->first());
        // more info here: relationship graph: https://imgur.com/a/RevHP44
    }

    public function deleteUserRecord()
    {
        $user = NewUser::where('id', 15);
        $user->delete();
        DD::dd($user);
    }

    public function homePage()
    {
//        $products = Product::select('id, name, description')->all();
        $products = Product::all();
        return view('home', [
            'products' => $products,
        ]);
    }

    public function selectUsingArray()
    {
        $product = Product::query()->select(['name', 'description'])->where('name', 'kkkkkkkk')->first();
        DD::dd($product->getAttributes());
    }

    public function selectUsingString()
    {
        $product = Product::query()->select('name, description')->where('name', 'kkkkkkkk')->first();
        DD::dl('selectUsingString');
        DD::dd($product->getAttributes());
    }

    // TODO unfinished
    public function selectWithJoin()
    {
        $product = MysqlConnection::getInst()
            ->query()
            ->select('name, description')
            ->where('name', 'kkkkkkkk')
            ->first();
        DD::dl('selectUsingString');
        DD::dd($product->getAttributes());
    }
}
