<?php

namespace Framework\validation\rules;

use Framework\Model;
use Framework\validation\BaseRule;

// TODO check filesize;ext;
//class FileRule extends RuleBase
//{
//
//    public function validate(Model $model, string $field, array $params): bool
//    {
//        if (isset($params['skipOnEmpty']) && in_array($params['skipOnEmpty'], [true, 'true', 1], true)) {
//            return true;
//        }
//        $fileIsSet = isset($_FILES[$field]) && !empty($_FILES[$field]['tmp_name']);
//        if ($fileIsSet) {
//            return true;
//        }
//
//        // TODO check if correct
//        $file = $_FILES[$field];
//        $minFileSize = $params['minFileSize'];
//        $maxFileSize = $params['maxFileSize'];
//        $extensionsArr = null;
//        // TODO or use existing validator
//        if ($file['size'] > $maxFileSize) {
//            // TODO add error msg
//        }
//        if ($file['size'] < $minFileSize) {
//            // TODO add error msg
//        }
//        if (isset($params['extensions'])) {
//            $extensionsArr = is_array($params['extensions']) ? $params['extensions'] : array($params['extensions']);
//            if (!in_array($file['ext'], $extensionsArr)) {
//                // TODO add error msg
//            }
//        }
//        if ($model->hasError($field)) {
//            return false;
//        }
//
//        return true;
//    }
//
//    public function getMessage(Model $model, string $field, array $params): string
//    {
//        return sprintf("File does is not selected");
//    }
//}
