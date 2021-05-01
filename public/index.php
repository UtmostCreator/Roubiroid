<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . './../vendor/autoload.php';

use app\controllers\SiteController;
use app\core\Application;



$app = new Application(dirname(__DIR__));

$app->router->get('/', 'home');

//$app->router->get('/contact', 'contact'); // render view
$app->router->get('/contact', [new SiteController(), 'contact']); // render class method
$app->router->post('/contact', [new SiteController(), 'handleContact']);

$app->run();