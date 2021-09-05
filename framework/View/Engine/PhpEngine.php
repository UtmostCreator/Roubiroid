<?php

namespace Framework\View\Engine;

use Framework\View\View;
use Modules\DD;

/* @description is to add the following functions:
 * Avoiding XSS hazards
 * Extending layout templates
 * Including partial templates
 * Adding a way to extend templates with "macros"
 */
class PhpEngine implements EngineInterface
{
    use HasManager;

    protected array $layouts = [];

    /**
     * @throws \Exception
     */
    public function render(View $view): string
    {
//        $contents = file_get_contents($path); // this will not see any variables inside a method/function/file
        extract($view->data);
        // buffering output allows us to use variables
        ob_start();
        require($view->path);
        $contents = ob_get_contents();
        ob_end_clean();
//        DD::dd($this->layouts);
        if ($layout = $this->layouts[$view->path] ?? null) {
            $contentsWithLayout = view(
                $layout,
                array_merge($view->data, ['contents' => $contents]) // the same as down below

            );

            return $contentsWithLayout;
        }

        return $contents;
    }

    public function __call($name, $values)
    {
        return $this->manager->useMacro($name, ...$values);
    }

    public function extends(string $template): self// :static
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $this->layouts[realpath($backtrace[0]['file'])] = $template;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function includes(string $template, $data = []): void
    {
        print view($template, $data);
    }
}
