<?php

namespace Framework\View\Engine;

use Framework\helpers\FileHelper;
use Framework\Paths;
use Framework\View\View;
use Modules\DD;

/* @description is to add the following functions:
 * Avoiding XSS hazards
 * Extending layout templates
 * Including partial templates
 * Adding a way to extend templates with "macros"
 */
class AdvancedEngine implements EngineInterface
{
    use HasManager;

    protected array $layouts = [];

    /**
     * @throws \Exception
     */
    public function render(View $view): string
    {
        $hash = md5($view->path);
        $folder = base_path() . '/storage/framework/views';
        if (FileHelper::canADirectoryBeCreated($folder)) {
            FileHelper::createDir($folder);
        }

        if (!is_file("{$folder}/{$hash}.php")) {
            touch("{$folder}/{$hash}.php");
        }

        $cached = realpath("{$folder}/{$hash}.php");

        $fileNotExistOrItIsOutdated = !file_exists($hash) || filemtime($view->path) > filemtime($hash);
        if ($fileNotExistOrItIsOutdated) {
            $content = $this->compile(file_get_contents($view->path));
            file_put_contents($cached, $content);
        }

        extract($view->data);
        ob_start();
        require($cached);
        $contents = ob_get_contents();
        ob_end_clean();

        if ($layout = $this->layouts[$cached] ?? null) {
            $contentsWithLayout = view(
                $layout,
                array_merge($view->data, ['contents' => $contents]) // the same as down below
            );

            return $contentsWithLayout;
        }

        return $contents;
    }

    // replace DSL bits with plain PHP...
    protected function compile(string $template): string
    {
        /*
        REGEXP look [ahead|behind]
            X(?=Y)      Positive lookahead      X if followed by        Y
            X(?!Y)      Negative lookahead      X if NOT followed by    Y
            (?<=Y)X     Positive lookbehind     X if after              Y
            (?<!Y)X     Negative lookbehind     X if NOT after          Y
        */

        // replace `@if` with `if(...):`
        // (?<=\() - Look Behind - says that it must be preceded
        // .* - takes everything after '(' and before ')' - see below why
        // (?=\) - must ends with ')'
        $paramsRegExp = '\(((?<=\().*(?=\)))\)';

        // remove comments
        $template = preg_replace("#<!--.*-->#", '', $template);
        $template = preg_replace("#\{\{--.*--\}\}#", '', $template);
//        preg_match_all("#\$(?![0-9])[a-zA-Z0-9]+#", $template, $matches);
//        DD::dd($matches);

//        $comments = '(?<=\/\/)';

        // replace `@extends` with `$this->extends`
        $template = preg_replace_callback("#@extends{$paramsRegExp}#", function ($matches) {
            return PHP_EOL . '<?php $this->extends(' . $matches[1] . '); ?>'; // $matches[1] - group; $matches[0] whole match
        }, $template);

        $template = preg_replace_callback("#@if{$paramsRegExp}#", function ($matches) {
            return PHP_EOL . '<?php if (' . $matches[1] . ') : ?>';
        }, $template);

        // replace `@endif` with `endif;`
        $template = preg_replace_callback('#@endif#', function ($matches) {
            return '<?php endif; ?>';
        }, $template);

        // replace `@endif` with `endif;`
        $template = preg_replace_callback('#@else#', function ($matches) {
            return '<?php else : ?>';
        }, $template);

        // replace `@foreach` with `foreach(...):`
        $template = preg_replace_callback("#@foreach{$paramsRegExp}#", function ($matches) {
            return PHP_EOL . '<?php foreach (' . $matches[1] . ') : ?>';
        }, $template);

        // replace `@endforeach` with `endforeach;`
        $template = preg_replace_callback('#@endforeach#', function ($matches) {
            return '<?php endforeach; ?>';
        }, $template);

        // replace `@[anything](...)` with `$this->[anything](...)`
        // default regex: "#\s+@([^(]+){$paramsRegExp}#"
        // custom regex: "#\s+(?<=@)([^(]+){$paramsRegExp}#"
        $template = preg_replace_callback("#(?<=\?>)[\s+\S+]+@([^(]+){$paramsRegExp}#", function ($matches) {
            return '<?php $this->' . $matches[1] . '(' . $matches[2] . '); ?>';
        }, $template);

        // replace `{{ ... }}` with `print $this->escape(...)`
        $template = preg_replace_callback('#\{\{([^}]*)\}\}#', function ($matches) {
            return '<?php print $this->escape(' . $matches[1] . '); ?>';
        }, $template);

        // replace `{!! ... !!}` with `print ...`
        $template = preg_replace_callback('#\{!!([^}]+)!!\}#', function ($matches) {
            return '<?php print ' . $matches[1] . '; ?>';
        }, $template);

        return $template . PHP_EOL;
    }

    public function extends(string $template): self// :static
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $this->layouts[realpath($backtrace[0]['file'])] = $template;
        return $this;
    }

    public function __call($name, $values)
    {
        return $this->manager->useMacro($name, ...$values);
    }

    /**
     * @throws \Exception
     */
    protected function include(string $template, $data = []): void
    {
        print view($template, $data);
    }
}
