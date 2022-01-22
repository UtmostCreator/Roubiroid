<?php

namespace Framework;

use Modules\DD;

/**
 * Class Request
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package Framework
 */
class Request
{
    private ?string $uri = null;
    private ?array $urlParts = [];
    private string $requestPath;

    public bool $hasParams = false;

    // METHOD info
    private string $method = 'get';
    private ?string $referer = null;

    public function __construct()
    {
        // decodes
        // from http://php-c-framework/products/1/view/foo%20bar%40baz
        // to   http://php-c-framework/products/1/view/foo bar@baz
        $this->uri = rawurldecode($_SERVER['REQUEST_URI']);
        $this->urlParts = isset($this->uri) ? parse_url($this->uri) : [];
        $this->referer = $_SERVER['HTTP_REFERER'] ?? null;
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
        $this->requestPath = $this->uri ?? '/';
        if ($this->urlParts && strpos($this->requestPath, '?')) {
            $this->requestPath = substr($this->requestPath, 0, strpos($this->requestPath, '?'));
        }
//        DD::dd($this->urlParts);
        $this->hasParams = $this->urlParts['query'] ?? false;
//        DD::dl($this->hasParams);

//        DD::dl($this->isGet);
//        parse_str($this->urlParts['query'], $query); // parses string into vars
//        DD::dl($query['name']);
//        DD::dl($query['id']);
//        DD::dd($this->urlParts); // path - method; query - prams string
    }

    public function getPath()
    {
        $path = $this->uri ?? '/';

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

    public function getUri(): string
    {
        return $this->uri;
    }

    public function refererPage(): string
    {
        return $this->referer ?? '';
    }
}
