<?php

namespace Framework\routing;

use Framework\BaseObject;
use Framework\helpers\StringHelper;
use Framework\Controller;
use Modules\DD;

class Route extends BaseObject
{
    protected array $parameters = [];
    protected string $method;
    protected string $path;
    protected ?string $name = null;
    // TODO check if can be replaced with a simple array or primitive type
    protected RouteHandler $handler;
    private int $priority;

    /**
     * @param string $method
     * @param string $path
     * @param RouteHandler $handler [TODO it can \Closure or callable, but then a simple path to a view file would not work!]
     */
    public function __construct(string $method, string $path, ?RouteHandler $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
    }

    public function name(string $name = null)
    {
        if ($name) {
            $this->name = $name;
            return $name;
        }
        return $this->name;
    }

    /* @description
     * 1. if the route has a simple path (like home) and we have a sting
     * match to the request path, them the route is match.
     * 2. if the rouge doesn't have any parameters (tha tbit where we're
     * checking for * or ? in the route path), then it can't be a match.
     * Remember that we know it wasn't a literal match if it reaches this point
     * 3. if the route path pattern matches the request path (after
     * the names have been replaced with nameless regular expression bits),
     * then we can assume it's a match
     *
     */
    public function matches(string $method, string $path): bool
    {
        if (
            $this->method === $method
            && $this->path === $path
        ) {
            return true;
        }

        $parameterNames = [];

        /*
        * Normalizing the path
        * Examples:
        * '' becomes '/'
        * 'home' becomes '/home/'
        * 'product/{id}' becomes '/product/{id}/'
        */
        $pattern = $this->normalisePath($this->path);

        /*
         * get all the parameter names and replace them with
         * regular expression syntax, to match optional or
         * required parameters
         * e.g.:
         * '/home/' remains '/home/'
         * '/product/{id}/' becomes '/product/([^/]+)/'
         * '/blog/{slug?}/' becomes '/product/([^/]*)(?:/?)/'
         * '/products/{page?}/{name?}/{text?}/' becomes '/products/([^/]*)(?:/?)([^/]*)(?:/?)([^/]*)(?:/?)'
        */
        $replaceParametersWithRegExpString = function (array $found) use (&$parameterNames) {
            array_push($parameterNames, rtrim($found[1], '?'));
            // $found[1] -- page? or page (if ? not specified)
            if (StringHelper::endsWith($found[1], '?')) {
                return '([^/]*)(?:/?)'; // if it's optional parameter, we make the following slash optional as well
            }

            return '([^/]+)/'; // for required parameters
        };
//        DD::dd($pattern);
        $findParametersRegExp = '#{([^}]+)}/#';
        $pattern = preg_replace_callback(
            $findParametersRegExp, // finds '{id}/' and '{slug?}/'
            $replaceParametersWithRegExpString,
            $pattern
        );
//        DD::dd($pattern);


        // if there are no route parameters, and it
        // wasn't a literal match, then this route
        // will never match the requested path
        if (
            !str_contains($pattern, '+')
            && !str_contains($pattern, '*')
        ) {
            return false;
        }
        preg_match_all("#{$pattern}#", $this->normalisePath($path), $matches);
        // let's assume we have the URL = http://php-c-framework/products/1/view/;
//        DD::dl($pattern); // e.g. /products/([^/]+)/
//        DD::dd($matches); // e.g [0]=> array(1) { [0]=> string(12) "/products/1/" } [1]=> array(1) { [0]=> string(1) "1" }
        $parameterValues = [];

        if (count($matches[1]) > 0) {
            // if the route matches the request path then
            // we need to assemble the parameters before
            // we can return true for the match

//            DD::dd($matches);
            foreach ($matches[1] as $value) {
                if (!empty($value)) {
                    array_push($parameterValues, $value);
                    continue;
                }

                array_push($parameterValues, null);
            }

            // make an empty array so that we can still
            // call array_combine with optional parameters
            // which may not have been provided
            $emptyValues = array_fill(0, count($parameterNames), false);

            // += syntax for arrays means: take values from the
            // right-hand side and only add them to the left-hand
            // side if the same key does't already exist.
            //
            // you'll usually want to use array_merge to combine
            // arrays, but this is an interesting use for +=
            $parameterValues += $emptyValues;

            // implode by equal "=" sign
            $this->parameters = array_combine($parameterNames, $parameterValues);

            return true;
        }

        return false;
    }

    public function normalisePath(string $path): string
    {
        $path = trim($path, '/');
        $path = "/{$path}/";
        $path = preg_replace('/[\/]{2,}/', '/', $path);

        return $path;
    }

    public function className()
    {
        return $this->handler->className;
    }

    public function methodName()
    {
        return $this->handler->methodName;
    }

    public function getActiveController(): ?Controller
    {
        $className = $this->className();
        $methodName = $this->methodName();

        if (!class_exists($className) || (class_exists($className) && !method_exists($className, $methodName))) {
            abort(500);
        }

        /** @var Controller $controller */
        $controller = new $className();
        $controller->action = $methodName;

        return $controller;
    }

    // these parameters mean the following
    // if we have '/products/view/{id}' which means '/products/view/88'
    // params would be this:
    // id = 88
    // or if we have '/products/view/{id?}' which means '/products/view/'
    // id = null
    public function parameters(): array
    {
        return $this->parameters;
    }

    // TODO
//    ->requirements(['page' => '\d+']);
//    ->defaults(['page' => 1])
    /* @var array $paramse .g. ['page' => '1']; */
//    public function defaults(array $params): Route
//    {
//
//    }

    /* @var array $paramse .g. ['page' => '\d+'];
     */
//    public function requirements(array $params): Route
//    {
//
//    }

    // if more than 1 matches, we use the priority
    // TODO add check for priority in matches/match method
    public function priority(int $value): Route
    {
        $this->priority = $value;

        return $this;
    }
}
