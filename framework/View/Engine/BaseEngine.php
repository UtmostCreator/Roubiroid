<?php

namespace Framework\View\Engine;

class BaseEngine implements EngineInterface
{
    public function render(string $path, array $data = []): string
    {
        $contents = file_get_contents($path);

        foreach ($data as $key => $value) {
            $contents = str_replace('{' . $key . '}', $value, $contents);
        }

        return $contents;
    }
}