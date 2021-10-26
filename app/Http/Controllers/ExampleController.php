<?php

namespace App\Http\Controllers;

use App\Models\NewUser;
use models\Product;
use models\Profile;
use Modules\DD;

class ExampleController
{

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
}
