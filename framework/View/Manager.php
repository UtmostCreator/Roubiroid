<?php

namespace Framework\View;

use Closure;
use Framework\View\Engine\EngineInterface;
use Modules\DD;

class Manager
{
    protected array $paths = [];
//    ['basic.php', 'advanced.php']
    protected array $engines = [];
    protected array $macros = [];

    public function addPath(string $path): self// : static
    {
        if (!is_dir($path)) {
            throw  new \InvalidArgumentException(sprintf('Invalid path to views folder %s', $path));
        }
        array_push($this->paths, $path);
        return $this;
    }

    public function addEngine(string $extension, EngineInterface $engine): self// : static
    {
        $this->engines[$extension] = $engine;
        $this->engines[$extension]->setManager($this);
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

    /**
     * @param string $template path to a view file
     * @param array $data data to be passed/rendered in view
     * @return View object that contains data and path to view (template)
     * @throws \Exception
     */
    public function resolve(string $template, array $data): View
    {
        foreach ($this->engines as $extension => $engine) {
            foreach ($this->paths as $path) {
                $file = "{$path}/{$template}.{$extension}";
                if (is_file($file) && !is_dir($file)) {
                    return new View($engine, realpath($file), $data);
                }
            }
        }
        throw new \Exception("Could not resolve '{$template}'");
    }

    public function addMacro(string $name, Closure $closure): self
    {
        $this->macros[$name] = $closure;
        return $this;
    }

//            function sum(...$numbers) {
//                $acc = 0;
//                foreach ($numbers as $n) {
//                    $acc += $n;
//                }
//                return $acc;
//            }
//
//            echo sum(1, 2, 3, 4); // 10
    public function useMacro(string $name, ...$values)
    {
        // $this->macros[$name] is a Closure, and we use it as a function
        if (isset($this->macros[$name])) {
            // we bind the closure so that $this
            // inside a macro refers to the view object
            // which means $data and $path can be used,
            // and you can get back to the $engine...
            $bound = $this->macros[$name]->bindTo($this);

//            function add($a, $b) {
//                return $a + $b;
//            }
//
//            echo add(...[1, 2])."\n"; // 3
//
//            $a = [1, 2];
//            echo add(...$a); // 3
            return $bound(...$values); // calling Closure with parameters unpacked from array e.g. ($param1, $param2)
        }

        throw new \Exception("Macro isn't defined: '{$name}'");
    }
}
