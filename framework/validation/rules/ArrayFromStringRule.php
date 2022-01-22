<?php

namespace Framework\validation\rules;

use Framework\validation\BaseRule;

//class ArrayFromStringRule extends RuleBase
//{
//
//    public function validate(Model $model, string $field, array $params):bool
//    {
//        if (!is_string($data)) {
//            throw new \InvalidArgumentException('must be a string!');
//        }
//        if (!isset($rule['separated']) || !isset($rule['fill'])) {
//            throw new \InvalidArgumentException('Rules::SEPARATEVALUE_WITH must set the "separated" and "fill"');
//        }
//
//        if (empty($data)) {
//            break;
//        }
//
//        $data = Sanitizer::string($data);
//
//        $pos = ArrayHelper::strposa($this->{$attr}, $rule['separated']);
////                    DD::dd($pos);
//        if ($pos >= 0) {
//            $this->{$rule['fill']} = explode($rule['separated'][$pos], $data);
//        }    }
//
//    public function getMessage(Model $model, string $field, array $params): string
//    {
//        return "Something went wrong, default value is not set";
//    }
//}
