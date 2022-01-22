<?php

namespace Framework\db\Migration;

use Framework\db\Connection\Connection;
use Framework\db\Migration\Field\BoolField;
use Framework\db\Migration\Field\DateTimeField;
use Framework\db\Migration\Field\FloatField;
use Framework\db\Migration\Field\IdField;
use Framework\db\Migration\Field\IntField;
use Framework\db\Migration\Field\StringField;
use Framework\db\Migration\Field\TextField;

abstract class Migration
{
    public const CREATE = 'create';
    public const ALTER = 'alter';
    protected array $fields = [];

    abstract public function connection(): Connection;

    abstract public function execute(): void;

    // TODO Sqlite must throw an exception as it does not allow to do it!
    abstract public function dropColumn($name): self;

    public function bool(string $name): BoolField
    {
        $field = $this->fields[] = new BoolField($name);
        return $field;
    }

    public function dateTime(string $name): DateTimeField
    {
        $field = $this->fields[] = new DateTimeField($name);
        return $field;
    }

    public function float(string $name): FloatField
    {
        $field = $this->fields[] = new FloatField($name);
        return $field;
    }

    public function id(string $name): IdField
    {
        $field = $this->fields[] = new IdField($name);
        return $field;
    }

    public function int(string $name): IntField
    {
        $field = $this->fields[] = new IntField($name);
        return $field;
    }

    public function string(string $name): StringField
    {
        $field = $this->fields[] = new StringField($name);
        return $field;
    }

    public function text(string $name): TextField
    {
        $field = $this->fields[] = new TextField($name);
        return $field;
    }
}
