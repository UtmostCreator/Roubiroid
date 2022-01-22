<?php

namespace Framework;

// TODO add priority to Rules inside a specific Rule class
abstract class Rules
{
    public const REQUIRED = 'required';
    public const MIN_STR = 'min_str';
    public const MAX_STR = 'max_str';
    public const MIN_NUM = 'min_num';
    public const MAX_NUM = 'max_num';
    public const MATCH = 'match';
    public const EMAIL = 'email';
    public const UNIQUE = 'unique';
    public const IN_ARRAY = 'in_array';
    public const ARRAY_FROM_STR = 'array_from_string';
    public const DEFAULT_VALUE = 'default_value';
//    public const UNIQUE_TOO_SHORT = 'unique_too_short';
    public const MSG_UNIQUE_TOO_SHORT = 'unique_too_short';

    public const MIN_VALUE_TO_CHECK_UNIQUENESS = 3;
}
