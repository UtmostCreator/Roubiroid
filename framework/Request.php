<?php

namespace App\core;

use modules\DD\DD;

/**
 * Class Request
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package App\core
 */
class Request
{
    private ?array $urlParts = [];
    private string $requestPath;

    public bool $hasParams = false;

    // METHOD info
    private string $method = 'get';

    public function __construct()
    {
        $this->urlParts = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI']) : [];
        $this->requestPath = $_SERVER['REQUEST_URI'] ?? '/';
        if ($this->urlParts && strpos($this->requestPath, '?')) {
            $this->requestPath = substr($this->requestPath, 0, strpos($this->requestPath, '?'));
        }
//        DD::dd($this->urlParts);
        $this->hasParams = $this->urlParts['query'] ?? false;
//        DD::dl($this->hasParams);
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';

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

        return $this->requestPath;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isGet(): bool
    {
        return $this->method === 'get';
    }

    public function isPost(): bool
    {
        return $this->method === 'post';
    }

    public function getBody()
    {
        $body = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }
}
