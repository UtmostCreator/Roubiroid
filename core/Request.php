<?php


namespace app\core;


use modules\DD\DD;

/**
 * Class Request
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core
 */
class Request
{
    private $urlParts = [];

    public bool $hasParams = false;

    // METHOD info
    private string $method = 'get';
    private bool $isGet = true;
    private bool $isPost = false;

    public function __construct()
    {
        $this->urlParts = parse_url($_SERVER['REQUEST_URI']);
        if (strpos($this->urlParts['path'], '?')) {
            $this->urlParts['path'] = substr($this->urlParts['path'], 0, strpos($this->urlParts['path'], '?'));
        }
//        DD::dd($this->urlParts);
        $this->hasParams = $this->urlParts['query'] ?? false;
//        DD::dl($this->hasParams);
        $this->method = strtolower($_SERVER['REQUEST_METHOD']) === 'get' ? 'get' : 'post';
        $this->isGet = $this->method === 'get';
        $this->isPost = $this->method === 'post';
//        DD::dl($this->isGet);
//        parse_str($this->urlParts['query'], $query); // parses string into vars
//        DD::dl($query['name']);
//        DD::dl($query['id']);
//        DD::dd($this->urlParts); // path - method; query - prams string
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        if (!$this->hasParams) {
            return $path;
        }

        return $this->urlParts['path'];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isGet(): bool
    {
        return $this->isGet;
    }

    public function isPost(): bool
    {
        return $this->isPost;
    }
}