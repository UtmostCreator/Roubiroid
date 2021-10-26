<?php

namespace Framework\db;

use Modules\DD;

class Relationship
{
    public ModelCollector $collector;
    public string $method;

    /**
     * @param ModelCollector $collector
     * @param string $method
     */
    public function __construct(ModelCollector $collector, string $method)
    {
        $this->collector = $collector;
        $this->method = $method;
    }

    // TODO not sure if needed string $method here
//    public function __invoke(string $method, array $parameters = [])
    public function __invoke(array $parameters = [])
    {
        return $this->collector->{$this->method}(...$parameters);
    }

    public function __call(string $method, array $parameters = [])
    {
        return $this->collector->$method(...$parameters);
    }

}
