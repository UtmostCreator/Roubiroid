<?php

namespace app\core\form;

use app\core\form\elements\Input;
use app\core\form\elements\Textarea;
use app\core\Model;
use Exception;

class Form
{
    protected const DEFAULT_METHOD_CURRENT = true;
    protected static string $HTML;
    protected static array $ERRORS = [];

    public const ENCTYPE_UPLOAD_A_FILE = 'multipart/form-data';
    public const ENCTYPE_DEFAULT = 'application/x-www-form-urlencoded';

    public const OPTIONAL_PROPS = [
        'enctype' => [
            self::ENCTYPE_DEFAULT,
            self::ENCTYPE_UPLOAD_A_FILE,
            'text/plain'
        ],
        'novalidate' => ['novalidate'],
        'accept-charset' => [],
        'class' => [],
        'autocomplete' => ['on', 'off'],
        'rel' => [
            'external',
            'help',
            'license',
            'next',
            'nofollow',
            'noopener',
            'noreferrer',
            'opener',
            'prev',
            'search',
        ],
        'target' => [
            '_blank',
            '_self',
            '_parent',
            '_top',
        ]
    ];

    /**
     * ['target'=>'_blank', ...]
     * @param string $action
     * @param string $method
     * @param array $options
     * @return Form
     */
    public static function begin(string $action, string $method, array $options): self
    {

        $action = strlen($action) === 0 && self::DEFAULT_METHOD_CURRENT ? $_SERVER['REQUEST_URI'] : $action;
        self::$HTML = sprintf('<form action="%s" method="%s">', $action, $method) . PHP_EOL;
        self::$HTML = self::validateAttributes($options);
        echo self::$HTML;

        return new self();
    }

    public static function end(): string
    {
        return '</form>';
    }

    protected static function validateAttributes(array $options)
    {
        foreach ($options as $key => $value) {
            if (!isset(self::OPTIONAL_PROPS[$key])) {
                self::$ERRORS[] = "Incorrect property '" . ($value ? $value : $key) . "'";
                continue;
            }
            $pos = strpos(self::$HTML, '>');
            if (isset($value) && in_array($value, self::OPTIONAL_PROPS[$key])) {
                // inserts a string into position $pos
                self::$HTML = substr_replace(self::$HTML, " {$key}='{$value}'", $pos, 0);
            } else {
                self::$HTML = substr_replace(self::$HTML, " {$key}='{$value}'", $pos, 0);
            }
        }
        foreach (self::$ERRORS as $error) {
            // TODO add a Notification for existing class
            self::$HTML .= '<div class="alert alert-danger" role="alert">' . $error . '</div>' . PHP_EOL;
        }
        return self::$HTML;
    }

    /**
     * @throws Exception
     */
    public function input(Model $model, $fieldName, $options = []): Input
    {
//        echo ;
        return new Input($model, $fieldName, $options);
    }

    public function textarea(Model $model, $fieldName, $options = []): Textarea
    {
//        echo ;
        return new Textarea($model, $fieldName, $options);
    }

    public function test()
    {
        return 'this';
    }
}
