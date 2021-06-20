<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\notification\Message;
use app\core\Request;
use app\core\Response;
use app\models\ContactForm;
use modules\DD\DD;

/**
 * Class SiteController
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function handleContact(Request $request)
    {
        return 'handling submitted data with post';
    }

    public function home()
    {
//        DD::dd(Session::getAll());
        $params = ['name' => 'Some Value', 'arr' => ['terst', 'value']];
        return $this->render('home', $params);
    }

    public function contact(Request $request, Response $response)
    {
        $contact = new ContactForm();
        if ($request->isPost()) {
//            DD::dd($contact);
//            $contact->load($request->getBody()) && $contact->validate();
            $contact->load($request->getBody());
//            DD::dd($contact);
            if ($contact->validate() && $contact->send()) {
                Application::$app->session->setFlash(Message::SUCCESS, 'Contact Us', 'Thanks for reaching us!');
                return $response->redirect('/');
            }
        }
        return $this->render('contact', ['model' => $contact]);
    }

    public function clearPersistentFlashes()
    {
        Application::$app->session->destroyFlashesWhere(false);
        Application::$app->response->redirect('');
    }

}
