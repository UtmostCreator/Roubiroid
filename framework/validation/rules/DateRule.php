<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

//class DateRule extends RuleBase
//{
//
//    public function validate(Model $model, string $field, array $params): bool
//    {
//        $model->$field = strpos($model->$field, ' - ')
//            ? str_replace(' - ', ' ', $model->$field)
//            : $model->$field;
//        $timestamp = strtotime($model->$field);
//        if ($format == 'Y-m-d' && $model->$field == date('Y-m-d')) {
//            $newDate = date('Y-m-d H:i:s');
//        } else {
//// Creating new date format from that timestamp
//            $newDate = date($format, $timestamp);
//        }
//        $d = DateTime::createFromFormat($format, $newDate);
//        $isValid = $d && $d->format($format) == $newDate;
//        if ($newDate && $isValid) {
//            $model->$field = filter_var($newDate, FILTER_SANITIZE_STRING);
////            DD::dd($newDate);
//            return false;
//        }
//        return ;
//    }
//
//    public function getMessage(Model $model, string $field, array $params): string
//    {
//        return sprintf("%s should be an email", parent::fieldNameToUpperCase($field));
//    }
//}
