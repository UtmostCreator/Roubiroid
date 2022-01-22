<?php

namespace Framework\View;

use Framework\View\Engine\EngineInterface;

class View
{
    public string $path;
    public array $data = [];
    public EngineInterface $engine;

    /**
     * @param Engine\EngineInterface $engine rendering engine (template compiler) object
     * @param string $path path to existing file (view)
     * @param array $data data to be passed to View so that it can be processed and rendered
     */
    public function __construct(EngineInterface $engine, string $path, array $data)
    {
        $this->engine = $engine;
        $this->path = $path;
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->engine->render($this);
    }

    public static function get(EngineInterface $engine, string $file, array $data)
    {
        return new static($engine, $file, $data);
    }
}
