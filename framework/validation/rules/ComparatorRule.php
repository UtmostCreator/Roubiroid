<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

class ComparatorRule extends BaseRule
{

    public function validate(Model $model, string $field, array $params): bool
    {
        $customMsg = isset($errorMsg) && strlen($errorMsg) > 0 ? $errorMsg : '';
        $startMsg = "This value('$compareTo') must ";
        switch ($operatorStr) {
            case ">":
                $errorMsg = $customMsg ? $customMsg : $startMsg . "be greater than ('$ruleVal')";
                return ($compareTo > $ruleVal) ? false : $errorMsg;
            case ">=":
//                DD::dd($data);
                $errorMsg = $customMsg ? $customMsg : $startMsg . "be greater or equal to  ('$ruleVal')";
                return ($compareTo >= $ruleVal) ? false : $errorMsg;
            case "<":
                $errorMsg = $customMsg ? $customMsg : $startMsg . "be less than ('$ruleVal')";
                return ($compareTo < $ruleVal) ? false : $errorMsg;
            case "<=":
//                DD::dd($compareTo <= $ruleVal);
                $errorMsg = $customMsg ? $customMsg : $startMsg . "be less or equal to ('$ruleVal')";
                return ($compareTo <= $ruleVal) ? false : $errorMsg;
            case "=":
            case "==":
                $errorMsg = $customMsg ? $customMsg : $startMsg . "be equal to ('$ruleVal')";
                return ($compareTo == $ruleVal) ? false : $errorMsg;
            case "!=":
            case "<>":
                $errorMsg = $customMsg ? $customMsg : $startMsg . "not be equal to ('$ruleVal')";
                return ($compareTo != $ruleVal) ? false : $errorMsg;
            default:
                return 'Wrong operator supplied to Validator->operator(...) function';
        }
    }

    public function getMessage(Model $model, string $field, array $params): string
    {
        return "Compare error";
    }
}
