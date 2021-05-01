<?php


namespace app\controllers;


use app\core\Controller;
use app\core\Request;
use app\models\RegisterModel;
use modules\DD\DD;

class AuthController extends Controller
{
    public string $layout = 'auth';

    public function login()
    {
        $this->setLayout($this->layout);
        return $this->render('login');
    }

    public function register(Request $request)
    {
        $registeredModel = new RegisterModel();
        if ($request->isPost()) {
            $registeredModel->load($request->getBody());

//            DD::dd($registeredModel);

            if ($registeredModel->validate() && $registeredModel->register()) {
                return 'success!';
            }
            DD::dd($registeredModel->errors);

            // if there is/are error(s)
            return $this->render('register', [
                'model' => $registeredModel
            ]);
        }

        $this->setLayout($this->layout);
        return $this->render('register', [
            'model' => $registeredModel
        ]);

        DD::dd('register POST');
    }

}
