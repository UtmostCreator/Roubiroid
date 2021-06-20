<?php

namespace app\core\db;

use app\core\Application;
use app\core\Model;
use modules\DD\DD;

/**
 * Class DBModel
 *
 * @author Roman Zakhriapa <utmostcreator@gmail.com>
 * @package app\core\db
 */
abstract class DbModel extends Model
{
    abstract public static function tableName(): string;

    abstract public static function attributes(): array;

    abstract public static function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = static::attributes();

        $stmt = (new Query())->insert($tableName, $attributes, $this->getAssocArrayAttValue());

        return $stmt;
    }

    protected function getAssocArrayAttValue(): array
    {
        $arr = [];
        foreach (static::attributes() as $attrName) {
            $arr[$attrName] = $this->{$attrName};
        }

        return $arr;
    }

    public static function findOne($where, $mode = null)
    {
        // $calledBy = get_called_class() === static // the are equal!!!!
        $q = new Query();

        if (!$mode) {
            Query::$CLASS = static::class;
        }

        if (is_string($where)) {
            $where = ['id' => (int)$where];
        }

        $res = $q->select()->from(static::tableName())->where($where)->one();
        return $res;
    }

}
