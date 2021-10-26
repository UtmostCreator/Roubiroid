<?php

namespace Framework\db\Migration\Field;

class IntField extends Field
{
    public ?int $default = null;

    public function default(int $value): self
    {
        $this->default = $value;
        return $this;
    }
}
