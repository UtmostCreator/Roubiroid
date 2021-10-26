<?php

namespace Framework\db\Migration;

use Framework\db\Connection\Connection;
use Framework\db\Connection\MysqlConnection;
use Framework\db\Exception\MigrationException;
use Framework\db\Migration\Field\BoolField;
use Framework\db\Migration\Field\DateTimeField;
use Framework\db\Migration\Field\Field;
use Framework\db\Migration\Field\FloatField;
use Framework\db\Migration\Field\IdField;
use Framework\db\Migration\Field\IntField;
use Framework\db\Migration\Field\StringField;
use Framework\db\Migration\Field\TextField;
use Modules\DD;

class MysqlMigration extends Migration
{
    protected MysqlConnection $connection;
    protected string $table;
    protected string $type;
    protected array $drops = [];

    /**
     * @param MysqlConnection $connection
     * @param string $table
     * @param string $type
     */
    public function __construct(MysqlConnection $connection, string $table, string $type)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->type = $type;
    }


    public function connection(): Connection
    {
        // TODO: Implement connection() method.
    }

    public function execute(): void
    {
        $query = '';
        $fields = array_map(fn($field) => $this->stringForField($field), $this->fields);

        $primary = array_filter($this->fields, fn($field) => $field instanceof IdField);
        $primaryKey = isset($primary[0]) ? "PRIMARY KEY (`{$primary[0]->name}`)" : '';

        if ($this->type === Migration::CREATE) {
            $fields = join(PHP_EOL, array_map(fn($field) => "{$field},", $fields));
            $query = "
                CREATE TABLE `{$this->table}` (
                    {$fields}
                    {$primaryKey}
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        }

        if ($this->type === Migration::ALTER) {
            $fields = join(PHP_EOL, array_map(fn($field) => "{$field};", $fields));
            $drops = join(PHP_EOL, array_map(fn($field) => "DROP COLUMN `{$field}`;", $this->drops));
            $query = "
                ALTER TABLE `{$this->table}`
                {$fields}
                {$drops}
                ";
        }

//        var_dump($query);
//        exit;
        $statement = $this->connection->pdo()->prepare($query);
        $statement->execute();
    }

    private function stringForField(Field $field): string
    {
        $prefix = '';

        if ($this->type === 'alter') {
            $prefix = 'ADD';
        }

        if ($field->alter) {
            $prefix = 'MODIFY';
        }

        $prefixAndField = "{$prefix} `{$field->name}`";

        if ($field instanceof BoolField) {
            $template = "{$prefixAndField} tinyint(4)"; // tinyint(1) must be 1 instead of 4 most probably

            $template .= $this->getIfNullable($field);
            if ($field->default !== null) {
                $default = (int)$field->default;
                $template .= " DEFAULT {$default}";
            }

            return $template;
        }

        if ($field instanceof DateTimeField) {
            $template = "{$prefixAndField} datetime";

            $template .= $this->getIfNullable($field);

            if ($field->default === DateTimeField::CURRENT_TIMESTAMP) {
                $template .= " DEFAULT " . DateTimeField::CURRENT_TIMESTAMP;
            } elseif ($field->default !== null) {
                $template .= " DEFAULT '{$field->default}'";
            }

            return $template;
        }

        if ($field instanceof FloatField) {
            $template = "{$prefixAndField} float";

            $template .= $this->getIfNullable($field);
            $template .= $this->getIfDefaultIsSet($field);

            return $template;
        }

        if ($field instanceof IdField) {
            return "{$prefixAndField} int(11) unsigned NOT NULL AUTO_INCREMENT";
        }

        if ($field instanceof IntField) {
            $template = "{$prefixAndField} int(11)";

            $template .= $this->getIfNullable($field);
            $template .= $this->getIfDefaultIsSet($field);

            return $template;
        }

        if ($field instanceof StringField) {
            $template = "{$prefixAndField} varchar(255)";

            $template .= $this->getIfNullable($field);
            $template .= $this->getIfDefaultIsSet($field);

            return $template;
        }

        if ($field instanceof TextField) {
            return "{$prefixAndField} text";
        }

        throw new MigrationException("Unrecognised field type for {$field->name}");
    }

    /**
     * @param Field $field
     * @return string
     */
    private function getIfNullable(Field $field): string
    {
        if ($field->nullable) {
            return " DEFAULT NULL";
        }
        return "";
    }

    private function getIfDefaultIsSet(Field $field): string
    {
        if ($field->default !== null) {
            return " DEFAULT '{$field->default}'";
        }

        return "";
    }

    public function dropColumn($name): self
    {
        $this->drops[] = $name;
        return $this;
    }
}
