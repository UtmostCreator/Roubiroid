<?php

namespace App\Http\Controllers;

use Framework\Application;
use Framework\Controller;
use Framework\notification\Message;
use Framework\Request;
use Framework\routing\Router;
use models\User;
use Modules\DD;

class RegisterController extends Controller
{
    public string $layout = 'auth';

    public function handle(Request $request)
    {
        $user = new User();
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE, false);
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
//        DD::dd($_SESSION);
        if ($request->isPost()) {
            $user->load($request->getBody());

            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
                redirect('/');
            }

            // if there is/are error(s)
            return view('register-user/form', [
                'model' => $user
            ]);
        }

//        $this->setLayout($this->layout);
        return view('register-user/form', [
            'model' => $user
        ]);
    }
}