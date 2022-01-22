<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\Rules;
use Framework\validation\BaseRule;
use Modules\DD;

class UniqueRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        $minMatchLength = $params['min'] ?? Rules::MIN_VALUE_TO_CHECK_UNIQUENESS;
        if (strlen($model->$field) < $minMatchLength) {
            return false;
        }
        /** @var $className Model */
        $className = get_class($model);
        $tableName = $className::tableName();
        $value = $model->$field;

//        DD::dd($tableName);
        $record = ($model->getConnection())
            ->query()
            ->select(['id', $field])
            ->from($tableName)
            ->where($field, '=', $value)
//            ->where([$field => $value])
//                            ->andWhere(['firstname' => 'Roman'])
            ->first();
        return empty($record);
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        $minMatchLength = $params['min'] ?? Rules::MIN_VALUE_TO_CHECK_UNIQUENESS;
        if (strlen($model->$field) < $minMatchLength) {
            return sprintf(
                "%s must have at least %u character to be unique",
                parent::fieldNameToUpperCase($field),
                Rules::MIN_VALUE_TO_CHECK_UNIQUENESS
            );
        }
        return sprintf(
            "This %s address is already exist in the DB table",
//            parent::fieldNameToUpperCase($field),
            $model->getLabel($field)
        // $model::tableName()
        );
    }
}
