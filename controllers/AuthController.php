<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\notification\Message;
use app\core\Request;
use app\core\Response;
use app\models\LoginForm;
use app\models\User;
use modules\DD\DD;

class AuthController extends Controller
{
    public string $layout = 'auth';

    public function __construct()
    {
//        $this->registerMiddleware(new AuthMiddleware(['array', 'of', 'actions']));
        DD::dd(Application::$app->router);
//        $this->registerMiddleware(new AuthMiddleware([User::ROLE_ADMIN => ['profile', 'test']]));
        $this->registerMiddleware(new AuthMiddleware(['profile']));
        parent::__construct();
    }

    public function login(Request $request, Response $response)
    {
        $loginForm = new LoginForm();
        $this->setLayout($this->layout);

        if ($request->isPost()) {
            $loginForm->load($request->getBody());

            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
            }
        }

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

        if ($request->isPost()) {
            $user->load($request->getBody());

            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash(Message::SUCCESS, 'User eRegistration', 'Registered successfully', Message::ADMIN_VISIBLE);
                Application::$app->response->redirect('/');
            }

            // if there is/are error(s)
            return $this->render('register', [
                'model' => $user
            ]);
        }

        $this->setLayout($this->layout);
        return $this->render('register', [
            'model' => $user
        ]);
    }

    public function profile()
    {
        $this->setLayout('main');
        return $this->render('profile');
    }
}
