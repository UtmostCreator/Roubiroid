<?php

namespace App\Http\Controllers;

use Framework\Application;
use Framework\Controller;
use Framework\Model;
use Framework\notification\Message;
use Framework\Request;
use Framework\routing\Router;
use models\User;
use Modules\DD;

class RegisterController extends Controller
{
    public string $layout = 'auth';

    public function handle(Request $request): string
    {
        $user = new User();
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE, false);
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
//        DD::dd($_SESSION);
        if ($request->isPost()) {
            $user->scenario = Model::SCENARIO_EDIT;
            $user->loadToAttributes($request->getBody());
//            $user->validate();
//            DD::dd(1);
//            $user->validate();
            $user->validate() ? redirect('back') : '';
//            DD::dd('auth controller');
//            DD::dd($user->errors);
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash(Message::SUCCESS, 'User Registration', 'Registered successfully', Message::ADMIN_VISIBLE);
                redirect('/');
            }
        }

//        $this->setLayout($this->layout);
        return view('register-user/form', [
            'model' => $user
        ]);
    }
}