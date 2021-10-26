<?php

namespace Framework\db;

use Closure;
use Framework\db\Connection\Connection;
use Framework\db\Exception\ConnectionException;
use Modules\DD;

class Factory
{
    protected array $connectors;

    public function addConnector(string $alias, Closure $connector): self
    {
        $this->connectors[$alias] = $connector;
        return $this;
    }

    public function connect(array $config): Connection
    {
        // TODO replace type with only driver
        if (!isset($config['driver'])) {
            throw new ConnectionException('type is not defined');
        }

        $type = $config['driver'];

        if (isset($this->connectors[$type])) {
            return $this->connectors[$type]($config);
        }

        throw new ConnectionException('This type is either unregistered or is not supported');
    }

}