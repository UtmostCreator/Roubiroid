<?php

namespace Framework\helpers;

use Modules\DD;

function _matcher($m, $str)
{
    if (preg_match('/^hello (\w+)/i', $str, $matches)) {
        $m[] = $matches[1];
    }

    return $m;
}

class ArrayHelper
{

    /** Fills in the object's properties with the data from
     * the array using the keys and  values from array
     * and return the object
     *
     * @param $obj
     * @param array $data
     * @return object
     */
    public static function fillPropsFromArray(&$obj, array $data): object
    {
        foreach ($data as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->{$key} = trim($value);
            }
        }

        return $obj;
    }

    /** Checks if the array is associative array or not
     *
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr): bool
    {
        if (array() === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /** Finds the element in array, and returns its position number or key
     *
     * @param string $haystack
     * @param array $needle
     * @param int $offset
     * @return int
     */
    public static function getEncounteredItemPos($haystack, $needle, $offset = 0): int
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $key => $query) {
            if ($pos = strpos($haystack, $query, $offset) !== false) {
                return $key; // stops at the first true result
            }
        }
        return -1;
    }

    /** Checks weather the array has the string
     *
     * @param string $haystack
     * @param array $needle
     * @param int $offset
     * @return bool
     */
    public static function strposa(string $haystack, array $needle, int $offset = 0): bool
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $query) {
            if (strpos($haystack, $query, $offset) !== false) {
                return true; // stop on first true result
            }
        }
        return false;
    }

    public function removeValue(array $arr, $value): array
    {
        if (($key = array_search($value, $arr)) !== false) {
            unset($arr[$key]);
        }
        return $arr;
    }

    // add alias getSelectOptions | prepareForDropdown

    /** Prepares the array for select options from the given id name and value
     *
     * @param array $array
     * @param string $lookedID
     * @param string $lookedName
     * @return array
     */
    public static function getIdValueArray(array $array, string $lookedID = 'id', string $lookedName = 'name'): array
    {
        if (empty($array) || strlen($lookedID) === 0 || strlen($lookedName) === 0) {
            return [];
        }

        return array_combine(array_column($array, $lookedID), array_column($array, $lookedName));
    }

    /** Returns the full difference between 2 arrays by value
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function getArrayFullDifferenceByValue(array $arr1, array $arr2): array
    {
        return array_diff($arr1, $arr2) + array_diff($arr2, $arr1);
    }

    // 2nd array will override any values from 1st array

    /** Returns the full difference between 2 arrays by key
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function getArrayFullDifferenceByKey(array $arr1, array $arr2): array
    {
        return array_diff_key($arr1, $arr2) + array_diff_key($arr2, $arr1);
    }
}
