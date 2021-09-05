<?php

namespace Framework\View\Engine;

use Framework\View\Manager;
use Framework\View\View;

interface EngineInterface
{
    public function render(View $view): string;
    public function setManager(Manager $manager): self; // static
//    public function useMacro(string $name, $values): self; // static
}
