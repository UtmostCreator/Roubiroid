<?php

namespace App\Http\Controllers;

use Framework\Application;
use Framework\Controller;
use Framework\notification\Message;
use Framework\Request;
use Framework\Response;
use Framework\routing\Router;
use models\LoginForm;
use models\User;
use Modules\DD;

class AuthController extends Controller
{
    public string $layout = 'auth'; // auth.advanced

    public function __construct()
    {
//        $this->registerMiddleware(new AuthMiddleware(['array', 'of', 'actions']));
//        $this->registerMiddleware(new AuthMiddleware([User::ROLE_ADMIN => ['profile', 'test']]));
//        $this->registerMiddleware(new AuthMiddleware(['profile'])); // old way
        parent::__construct();
    }

    public function login(Request $request, Response $response)
    {
        $loginForm = new LoginForm();
//        $this->setLayout($this->layout);

        if ($request->isPost()) {
            $loginForm->load($request->getBody());

            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
            }
        }

//        return view('login', ['model' => $loginForm]);

        return $this->render('login', ['model' => $loginForm]);
    }

    public function logout(Request $request, Response $response)
    {
        if ($request->isPost()) {
            Application::$app->logout();
            $response->redirect('/');
        }
    }

    public function register(Request $request)
    {
        $user = new User();
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE, false);
//        Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
//        DD::dd($_SESSION);

//        DD::dd(Router::route('register-user'));
//        DD::dd($this->router);
//        DD::dd($_POST);
        if ($request->isPost()) {
            $user->load($request->getBody());

            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
//                Application::$app->response->redirect('/');
            }

            // if there is/are error(s)
            // old way
//            return $this->render('register', [
//                'model' => $user
//            ]);
        }

//        $this->setLayout($this->layout);

        return view('register', [
            'model' => $user
        ]);
        // old way
//        return $this->render('register', [
//            'model' => $user
//        ]);
    }

    public function profile()
    {
        $this->setLayout('main');
        return $this->render('profile');
    }
}
