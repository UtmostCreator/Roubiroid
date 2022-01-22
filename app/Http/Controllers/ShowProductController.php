<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\routing\Router;
use models\Product;
use Modules\DD;

class ShowProductController extends Controller
{

    public function oldViewProduct()
    {
        Router::route('product-list', ['page' => 2, 'name' => 'test']);
        return view('products/view', [
            'product' => 'test',
            'scary' => '<script>alert("boo!")</script>'
        ]);
//        DD::dd(Router::current()->parameters());
    }

    public function handle()
    {
        $params = Router::getActiveRoute()->parameters();
        $product = Product::find((int) $params['product']);
        return view('products/view', [
            'product' => $product,
            'orderAction' => $this->router->route('order-product', ['product'
            => $product->id]),
            'csrf' => csrf(),
        ]);
    }
}
