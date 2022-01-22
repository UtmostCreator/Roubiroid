<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateNewDbConnectionController;
use App\Http\Controllers\CsrfExampleProtectionController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ShowProductController;
use App\Http\Controllers\SiteController;
use Framework\exceptions\NotFoundException;
use Framework\routing\Router;

// SYSTEM|DEVELOPMENT ROUTES
// TODO add possibility for debuggin outputting the error message
// TODO e.g. if this is called new \InvalidArgumentException('no route with that name "' . $name . '"')
Router::addSystem(500, function () {
    $mainText = 'Please contact your administrator or your developer for further resolution';
    $noteText = "<span class='error-note'> Please include a steps to reproduce this error; The More info you provide the easier it to resolve</span>";
    throw new Exception(sprintf("%s<br>%s", $mainText, $noteText));
});

Router::addSystem(404, function () {
    throw new NotFoundException();
});

Router::addSystem(400, function () {
    echo 'Bad Request!';
//    throw new NotFoundException();
});

Router::get('/new-login', [AuthController::class, 'login'])->name('log-in-user-form');
Router::post('/new-login', [AuthController::class, 'login'])->name('log-in-user');