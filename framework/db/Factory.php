<?php

namespace Framework\db;

use Closure;
use Framework\db\Connection\Connection;
use Framework\db\Exception\ConnectionException;
use Framework\Support\DriverFactory;

class Factory implements DriverFactory
{
    protected array $drivers;

    public function addDriver(string $alias, Closure $driver): self
    {
        $this->drivers[$alias] = $driver;
        return $this;
    }

    public function connect(array $config): Connection
    {
        // TODO replace type with only driver
        if (!isset($config['driver'])) {
            throw new ConnectionException('type is not defined');
        }

        $type = $config['driver'];

        if (isset($this->drivers[$type])) {
            return $this->drivers[$type]($config);
        }

        throw new ConnectionException('This type is either unregistered or is not supported');
    }
}