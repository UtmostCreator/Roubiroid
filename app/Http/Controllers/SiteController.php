<?php

namespace App\Http\Controllers;

use Framework\Application;
use Framework\Controller;
use Framework\notification\Message;
use Framework\Request;
use Framework\Response;
use Framework\routing\Router;
use models\ContactForm;
use Modules\DD;

/**
 * Class SiteController
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package App\Controllers
 */
class SiteController extends Controller
{
    public function handleContact(Request $request)
    {
        return 'handling submitted data with post';
    }

    public function home()
    {
//        DD::dd(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1));
//        DD::dd(Session::getAll());
        $params = ['name' => 'Some Value', 'arr' => ['terst', 'value']];
        return $this->render('home', $params);
    }

    public function contact(Request $request, Response $response)
    {
        $contact = new ContactForm();
        $contact->scenario = ContactForm::SCENARIO_VISIBLE_ONLY;
//        $contact->email = 'test@gmail.com';
//        $contact->subject = 'some value';
//        $contact->body = 'new';
//        dd();
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

    public function listAdvanced()
    {
        $test = 123;
        return view('products/list', [
            'product' => 'test',
            "test" => $test,
            'scary' => '<script>alert("boo!")</script>'
        ]);
    }

}
