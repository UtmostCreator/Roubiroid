<?php

namespace Framework\View;

use Framework\View\Engine\EngineInterface;
use Modules\DD;

class Manager
{
    protected array $paths = [];
    protected array $engines = [];

    public function addPath(string $path): self// : static
    {
        array_push($this->paths, $path);
        return $this;
    }

    public function addEngine(string $extension, EngineInterface $engine): self// : static
    {
        $this->engines[$extension] = $engine;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function render(string $template, array $data = []): string
    {
        foreach ($this->engines as $extension => $engine) {
            foreach ($this->paths as $path) {
                $file = "{$path}/{$template}.{$extension}";

                if (is_file($file)) {
                    return $engine->render($file, $data);
                }
            }
        }
        throw new \Exception("Could not resolve '{$template}'");
    }
}
