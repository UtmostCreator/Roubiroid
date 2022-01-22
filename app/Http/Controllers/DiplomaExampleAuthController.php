<?php

namespace App\Http\Controllers;

use Framework\Controller;
use Framework\Request;
use models\LoginForm;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            secure();
            $loginForm->load($_POST);
            if ($loginForm->validate() && $loginForm->login()) {
                redirect('/');
            }
        }
        return view('login', [
            'logInAction' => $this->router->route('log-in-user'),
            'model' => $loginForm,
        ]);
    }
}
