<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\Request;
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
        $params = ['name' => 'Some Value', 'arr' => ['terst','value']];
        return $this->render('home', $params);
    }

    public function contact()
    {

        return Application::$app->router->renderView('contact');
    }

}
