<?php

namespace Framework\View\form\elements;

use Framework\helpers\StringHelper;
use Framework\Model;
use Exception;

abstract class Field
{
    // split on input categories
    protected const SINGLE_LINE_OPTIONS = [
        'required', 'checked', 'selected'
    ];
    protected const ADDITIONAL_OPTIONS = ['note'];
    protected const ALLOWED_OPTIONS = [
        ...self::SINGLE_LINE_OPTIONS,
        ...self::ADDITIONAL_OPTIONS,
        'type', 'value', 'placeholder', 'class', 'disabled', 'name', 'id', 'style',
        'autocomplete', 'accept', 'showImagePreview', 'pattern',
        'data-date-format', 'data-on', 'data-off', 'data-size'
    ];

    protected const HIDE_NOTE_ON_ERROR = true;
    protected const IS_BROWSER_REQUIRED = false;
    protected const DEFAULT_CLASS = 'form-control';
    protected const DEFAULT_WRAPPER_CLASS = 'mb-3';
    protected const VALID_CLASS = 'is-valid';
    protected const INVALID_CLASS = 'is-invalid';
    protected const INVALID_FEEDBACK_CLASS = 'invalid-feedback';
    protected const VALID_FEEDBACK_CLASS = 'valid-feedback';
    protected const REQUIRED_SIGN = '* ';
    protected string $wrapperSizeClass = 'col-md-12';

    protected string $id;
    protected string $attribute;
    protected string $value;
    protected string $note;
    protected array $options;
    protected string $type;
    protected Model $model;
    protected string $label;
    protected bool $required;
    protected string $optionsStr = '';
    protected string $openDiv = '';
    protected string $closeDiv = '';

    abstract protected function getField();

    public function __construct(Model $model, string $fieldName, array $options = [])
    {
        $this->id = '';
        $this->note = '';
        $this->type = '';
        $this->label = '';
        $this->required = '';
        try {
            $this->generalCheckIsValid($model, $fieldName, $options);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        $this->attribute = $fieldName;
        $this->options = $options;
        $this->model = $model;
        $this->prepareOptions();
        $this->attribute = $fieldName;
        $this->value = $this->model->{$this->attribute};
    }

    // TODO add extra icon
    public function __toString(): string
    {
        return sprintf(
            '
            <div class="%s">
                %s
                %s
                %s
            </div>
        ',
            implode(' ', [$this->wrapperSizeClass, self::DEFAULT_WRAPPER_CLASS]),
            $this->label,
            $this->getField(),
            $this->note(),
        );
    }

    protected function note(): string
    {
        if (empty($this->note) || $this->model->hasError($this->attribute) && self::HIDE_NOTE_ON_ERROR) {
            return '';
        }
        return sprintf('
            <div class="form-text text-muted">
              %s
            </div>', $this->note);
    }

    public function label($value = ''): Field
    {
        $value = !empty($value) ? $value : $this->model->getLabel($this->attribute);
        $requiredText = $this->required ? '<b class="text-danger">' . self::REQUIRED_SIGN . '</b>' : '';
        $this->label = sprintf(
            '<label for="%s" class="form-label">%s%s</label>',
            $this->id,
            $requiredText,
            $value,
        );
        return $this;
    }

    /**
     * @param Model $model
     * @param $name
     * @param array $options
     * @throws Exception
     */
    protected function generalCheckIsValid(Model $model, $name, array $options)
    {
        try {
            if (!property_exists($model, $name)) {
                throw new \InvalidArgumentException("There is no such " . $name . " filed in this model!");
            }
        } catch (Exception $exception) {
        }

        try {
            if (!is_array($options)) {
                $response = sprintf('Allowed options ("%s")', implode('", "', self::ALLOWED_OPTIONS));
                throw new \InvalidArgumentException($response);
            }
        } catch (Exception $exception) {
        }
    }

    protected function prepareOptions(): void
    {
        $this->options['name'] = $this->attribute;
        $this->options = $this->options + array_fill_keys(self::ALLOWED_OPTIONS, '');
        $this->options = array_map(function ($value) {
            if (is_string($value) || is_bool($value)) {
                return $value === "" ? null : $value;
            } else {
                return $value;
            }
        }, $this->options);

        $this->options['class'] = $this->getClasses();
        $this->options['placeholder'] = $this->getPreparedPlaceholder();
        $this->options['value'] = $this->getDefaultOptionValue();
        $this->fillObjProps();

        $this->getOptionsStr();
    }

    protected function getFeedback(): string
    {
        if (!$this->model->hasErrors()) {
            return '';
        }
        return sprintf('
            <div class="%s">
              %s
            </div>', $this->getValidationFeedbackClass(), $this->model->getFirstError($this->attribute));
    }

    protected function getIsRequiredStr(): string
    {
        return $this->required === true ? ' required' : '';
    }

    protected function getValidationFieldClass(): string
    {
        if (empty($_POST)) {
            return '';
        }
        return $this->model->hasError($this->attribute) ? self::INVALID_CLASS : self::VALID_CLASS;
    }

    protected function getValidationFeedbackClass(): string
    {
        return $this->model->hasError($this->attribute) ? self::INVALID_FEEDBACK_CLASS : self::VALID_FEEDBACK_CLASS;
    }

    protected function getPreparedPlaceholder(): string
    {
        return StringHelper::uppercaseWordsAndReplaceSpecifier('_', $this->attribute);
    }

    /** @return string|bool|int|float */
    protected function getDefaultOptionValue()
    {
        if (empty($this->model->{$this->attribute})) {
            return $this->options['value'];
        }
        if (is_object($this->model->{$this->attribute})) {
            return '';
        }
        return $this->model->{$this->attribute};
    }

    protected function getOptionsStr(): string
    {
        $this->options = array_filter($this->options);
        foreach ($this->options as $key => $value) {
            if (is_object($value)) {
                continue;
            }

            if (in_array($key, self::ADDITIONAL_OPTIONS)) {
                continue;
            }

            if (in_array($key, self::SINGLE_LINE_OPTIONS)) {
                if ($key === 'required' && !self::IS_BROWSER_REQUIRED) {
                    continue;
                }
                $this->optionsStr .= "$key ";
                continue;
            }
            if (strlen(trim($value)) > 0) {
                $this->optionsStr .= "$key='$value' ";
            }
        }
        $this->optionsStr = trim($this->optionsStr);
        return $this->optionsStr;
    }

    protected function getClasses(): string
    {
        $classesStr = [
            self::DEFAULT_CLASS,
            $this->getValidationFieldClass(),
            $this->options['class']
        ];
        $classesStr = array_filter($classesStr, fn($value) => !is_null($value) && $value !== '');
        return implode(' ', $classesStr);
    }

    protected function fillObjProps()
    {
        foreach ($this->options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = trim($value);
            }
        }
    }
}
