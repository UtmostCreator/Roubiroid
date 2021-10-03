<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\Request;
use Framework\routing\Router;
use Modules\DD;

class ListProductsController extends Controller
{

    public function handle(Request $request): string
    {

        $params = Router::getActiveRoute()->parameters();
        $params['page'] ??= 1;

        $next = Router::route('list-products', ['page' => $params['page'] + 1]);

        return view(
            'products/new-list',
            [
                'params' => $params,
                'next' => $next
            ]
        );
    }
}
