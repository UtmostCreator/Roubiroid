<?php

namespace Framework\db\Migration\Field;

class DateTimeField extends Field
{
    public const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';
    public ?string $default = null;

    public function default(string $value): self
    {
        $this->default = $value;
        return $this;
    }
}
