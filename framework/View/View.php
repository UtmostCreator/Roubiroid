<?php

namespace Framework\View;

use Modules\DD;

class View
{
    protected Engine\EngineInterface $engine;
    public string $path;
    public array $data = [];

    /**
     * @param Engine\EngineInterface $engine rendering engine (template compiler) object
     * @param string $path path to existing file (view)
     * @param array $data data to be passed to View so that it can be processed and rendered
     * @throws \Exception
     */
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
