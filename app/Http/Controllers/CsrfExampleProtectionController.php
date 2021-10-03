<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\Request;
use Modules\DD;

class CsrfExampleProtectionController extends Controller
{

    public function handle(Request $request)
    {
        if ($request->isPost()) {
            secure();
            DD::dd('success');
        }

        return view('csrf-example/csrf-form', []);
//        return redirect(Router::route('show-home-page'));
    }
}