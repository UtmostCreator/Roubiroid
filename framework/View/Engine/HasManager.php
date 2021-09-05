<?php

namespace Framework\View\Engine;

use Framework\View\Manager;

trait HasManager
{
    protected Manager $manager;

    public function setManager(Manager $manager): self
    {
        $this->manager = $manager;
        return $this;
    }
}
