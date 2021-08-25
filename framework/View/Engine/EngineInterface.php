<?php

namespace Framework\View\Engine;

interface EngineInterface
{
    public function render(string $path, array $data = []): string;
}