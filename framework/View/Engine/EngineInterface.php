<?php

namespace Framework\View\Engine;

use Framework\View\EngineManager;
use Framework\View\View;

interface EngineInterface
{
    public function render(View $view): string;
    public function setManager(EngineManager $manager): self; // static
//    public function useMacro(string $name, $values): self; // static
}
