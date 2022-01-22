<?php

namespace Framework\View\form\elements;

use Exception;
use Framework\Model;
use Modules\DD;

class Input extends Field
{
    public const FILE = 'file';
    public const TEXT = 'text';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
//    protected const ALLOWED_OPTIONS = [
//        ...parent::ALLOWED_OPTIONS,
//        'size'
//    ];

    // DEFAULT SETTINGS:
    protected const DEFAULT_TYPE = self::TEXT;
    protected string $openDiv = '<div class="input-group">';
    protected string $closeDiv = '</div>';
    protected const ALLOWED_TYPES = [self::FILE, self::TEXT, self::PASSWORD, self::EMAIL];

    /**
     * Input constructor.
     * @param Model $model
     * @param string $fieldName
     * @param array $options input attributes
     * @throws Exception
     */
    public function __construct(Model $model, string $fieldName, array $options = [])
    {
        $options['type'] ??= self::DEFAULT_TYPE;
//        $model->isRequired($fieldName);
        try {
            if (!in_array($options['type'], self::ALLOWED_TYPES)) {
                exit;
            }
        } catch (\InvalidArgumentException $exception) {
            $response = 'This type is not supported! See if there is another method for this type.';
            throw new \InvalidArgumentException($response);
        }
        parent::__construct($model, $fieldName, $options);
    }

    public function passwordField($eyeIcon = false): Input
    {
        $this->type = 'password';
        $this->options['showPasswordToggle'] = $eyeIcon;
        return $this;
    }

    protected function getField(): string
    {
        $input = '<input %s>%s';

        switch ($this->type) {
            case 'password':
                if (isset($this->options['showPasswordToggle'])) {
                    return sprintf(
                        '
                        <div class="input-group">
                          <input type="password" %s>
                          <button class="btn btn-outline-secondary" type="button" id="%s"><i class="fas fa-eye-slash"></i></button>
                          %s
                        </div>',
                        $this->optionsStr,
                        $this->id,
                        $this->getFeedback(),
                    );
                }
                break;
            case 'file':
                $input = $this->openDiv . $input . $this->closeDiv;
//        $this->getImagePreview($name, $model);
                break;
        }
        return sprintf(
            $input,
            $this->optionsStr,
            $this->getFeedback()
        );
    }
}
