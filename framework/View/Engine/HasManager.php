<?php

namespace Framework\View\Engine;

use Framework\View\EngineManager;

trait HasManager
{
    protected EngineManager $manager;

    public function setManager(EngineManager $manager): self
    {
        $this->manager = $manager;
        return $this;
    }
}
