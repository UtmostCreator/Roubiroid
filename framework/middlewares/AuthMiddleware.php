<?php

namespace Framework\middlewares;

use Framework\Application;
use Framework\exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     * @param string[] $actions
     */
//    public function __construct(array $actions = [])
//    {
//        $this->actions = $actions;
//    }
//
//    public function execute()
//    {
//        if (Application::isGuest()) {
//            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
//                throw new ForbiddenException();
//            }
//        }
//    }

    public function execute()
    {
        if (empty(app()->user)) {
            throw new ForbiddenException();
        }
    }
}
