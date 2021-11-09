<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\routing\Router;
use models\Product;

class OrderProductController extends Controller
{

    public function handle(Router $router)
    {
        secure();

        // use $data to create a database record...

        $_SESSION['ordered'] = true;

        return redirect($router->route('homePage'));
    }}