<?php

namespace Framework\Support;

use Framework\Application;
use Framework\helpers\Config;

abstract class DriverProvider
{
    public function bind(Application $app): void
    {
        $name = $this->name();
        $factory = $this->factory();
        $drivers = $this->drivers();

        $app->bind($name, function ($app) use ($name, $factory, $drivers) {
            foreach ($drivers as $key => $value) {
                $factory->addDriver($key, $value);
            }

            return $factory->connect(Config::get('connections=default'));
        });
    }

    abstract protected function name(): string;
    abstract protected function factory(): DriverFactory;
    abstract protected function drivers(): array;
}
