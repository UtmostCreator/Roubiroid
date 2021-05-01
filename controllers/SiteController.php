<?php


namespace app\controllers;


use app\core\Application;

/**
 * Class SiteController
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\controllers
 */
class SiteController
{
    public function handleContact()
    {
        return 'handling submitted data with post';
    }

    public function home()
    {
        $params = ['name' => 'Some Value', 'arr' => ['terst','value']];
        return Application::$app->router->renderView('home', $params);
    }

    public function contact()
    {

        return Application::$app->router->renderView('contact');
    }

}
