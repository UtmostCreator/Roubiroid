<?php

namespace Framework\View;

class View
{
    protected Engine\EngineInterface $engine;
    public string $path;
    public array $data = [];

    public function __construct(Engine\EngineInterface $engine, string $path, array $data)
    {
        $this->engine = $engine;
        $this->path = $path;
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->engine->render($this);
    }

}