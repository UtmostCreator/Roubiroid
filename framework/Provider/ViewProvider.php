<?php

namespace Framework\Provider;

use Framework\Application;
use Framework\View\Engine\AdvancedEngine;
use Framework\View\Engine\BasicEngine;
use Framework\View\Engine\PhpEngine;
use Framework\View\EngineManager;

class ViewProvider
{
    public function bind(Application $app): void
    {
        $app->bind('view', function ($app) {
            $manager = new EngineManager();

            $this->bindPaths($app, $manager);
            $this->bindMacros($app, $manager);
            $this->bindEngines($app, $manager);

            return $manager;
        });
    }

    private function bindPaths(Application $app, EngineManager $manager): void
    {
        $manager->addPath($app->resolve('paths.base') . '/resources/views');
        $manager->addPath($app->resolve('paths.base') . '/resources/images');
    }

    private function bindMacros(Application $app, EngineManager $manager): void
    {
        $manager->addMacro('escape', fn($value) => htmlspecialchars($value, ENT_QUOTES));
        $manager->addMacro('includes', fn(...$params) => print view(...$params));
    }

    private function bindEngines(Application $app, EngineManager $manager): void
    {
        $app->bind('view.engine.advanced', fn() => new AdvancedEngine());
        $app->bind('view.engine.basic', fn() => new BasicEngine());
        $app->bind('view.engine.php', fn() => new PhpEngine());
//        $app->bind('view.engine.literal', fn() => new LiteralEngine());

        $manager->addEngine('advanced.php', $app->resolve('view.engine.advanced'));
        $manager->addEngine('basic.php', $app->resolve('view.engine.basic'));
        $manager->addEngine('php', $app->resolve('view.engine.php'));
//        $manager->addEngine('svg', $app->resolve('view.engine.literal'));
    }
}
