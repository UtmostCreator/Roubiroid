<?php

namespace Framework\View\Engine;

/* @description is to add the following functions:
 * Avoiding XSS hazards
 * Extending layout templates
 * Including partial templates
 * Adding a way to extend templates with "macros"
 */
class PhpEngine implements EngineInterface
{
    protected string $path;
    protected ?string $layout;
    protected string $contents;

    public function render(string $path, array $data = []): string
    {
        $this->path = $path; // assigning $path here to avoid collision after extract function

//        $contents = file_get_contents($path); // this will not see any variables inside a method/function/file
        extract($data);
        // buffering output allows us to use variables
        ob_start();
        require($this->path);
        $contents = ob_get_contents();
        ob_end_clean();

        if ($this->layout) {
            $__layout = $this->layout;
            $this->layout = null;
            $this->contents = $contents;

            $contentsWithLayout = view($__layout, $data);

            return $contentsWithLayout;
        }

        return $contents;
    }

    public function escape(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES);
    }

    public function extends(string $template): self// :static
    {
        $this->layout = $template;
        return $this;
    }
}
