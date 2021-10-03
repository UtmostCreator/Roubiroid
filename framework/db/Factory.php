<?php

namespace Framework\db;

use Closure;
use Framework\db\Connection\Connection;
use Framework\db\exception\ConnectionException;

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
        if (!isset($config['type'])) {
            throw new ConnectionException('type is not defined');
        }

        $type = $config['type'];

        if (isset($this->connectors[$type])) {
            return $this->connectors[$type]($config);
        }

        throw new ConnectionException('This type is either unregistered or is not supported');
    }

}