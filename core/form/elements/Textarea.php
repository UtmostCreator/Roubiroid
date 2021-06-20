<?php


namespace app\core\form\elements;


use app\core\Model;

class Textarea extends Field
{
    protected const ALLOWED_OPTIONS = [
        ...parent::ALLOWED_OPTIONS,
        'size', 'rows'
    ];

    public function __construct(Model $model, string $fieldName, array $options = [])
    {
        parent::__construct($model, $fieldName, $options);
    }

    protected function getField(): string
    {
        $textarea = '<textarea %s>%s</textarea>%s';

        return sprintf(
            $textarea,
            $this->optionsStr,
            $this->options['value'] ?? '',
            $this->getFeedback()
        );
    }
}