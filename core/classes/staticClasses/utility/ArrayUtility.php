<?php

namespace DarlingCms\classes\staticClasses\utility;

/**
 * Class ArrayUtility. This class provides a collection of static utility methods for working with arrays.
 * @package DarlingCms\classes\staticClasses\utility
 */
class ArrayUtility
{
    /**
     * Returns the key of a specified value in the specified array.
     *
     * Note: If the value exists at more than key, the key for the first occurrence
     *       is returned.
     *
     * Note: If the specified value does not exist in the specified array this method
     *       will return false.
     *
     * Examples:
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz', 'Foo')); // returns 2
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz', 'FooKey' => 'Foo')); // returns FooKey
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz')); // returns false, Foo does not exist!
     *
     * @param mixed $value The value whose key should be returned
     *
     * @param array $array The array to search.
     *
     * @param bool $strict (Optional)Determines whether not the specified $value should
     *                               be compared with the values in the specified $array
     *                               strictly.
     *
     *                               If set to true, comparison will be strict (===).
     *
     *                               If false it will be loose (==).
     *
     *                               (Defaults to false)
     *
     * @return bool|int|string The value's key, or false if the value's key could not be
     *                         determined.
     *
     */
    public static function getValuesKey($value, array $array, $strict = false)
    {
        // Determine value's key in the array
        foreach ($array as $key => $val) {
            switch ($strict) {
                case true:
                    if ($val === $value) {
                        return $key;
                    }
                    break;
                default:
                    if ($val == $value) {
                        return $key;
                    }
                    break;
            }
        }
        error_log(sprintf('IHtmlPage implementation error: Failed to determine key of value "%s" in the specified array.', serialize($value)));
        return false;
    }

    /**
     * Splits an array into two arrays at the specified value.
     *
     * Note: Which of the two arrays the $value ends up in is determine by the $splitBefore
     *       parameter. If set to true, the $value will be placed at the end of the first
     *       of the two resulting arrays, if set to false the $value will placed at the
     *       beginning of the second of the two resulting arrays.
     *
     *       For example:
     *
     *           splitArrayAtValue(array(1,2,3,4), 3, $splitBefore = true)
     *           // returns array(array(1,2,3), array(4))
     *
     *           splitArrayAtValue(array(1,2,3,4), 3, $splitBefore = false)
     *           // returns array(array(1,2), array(3,4))
     *
     * @param array $array The array to split.
     *
     * @param mixed $value The value to split the array at.
     *
     * @param bool $splitBefore Determines which of the two resulting arrays the $value
     *                          will end up in. If set to true, the $value will be the
     *                          last item in the first of the two resulting arrays, if
     *                          set to false the $value will be the first item in the
     *                          second of the two resulting arrays.
     *
     * @return array An array containing the arrays resulting from the split. The
     *               first array at index 0 will contain the values that came before
     *               the specified $value, and the second array at index 1 will contain
     *               the values that came after the specified $value.
     *
     *               <br>Example: array(0 => [VALUES BEFORE VALUE], 0 => [VALUES AFTER VALUE])
     *
     *               <br>Also Note: The $splitBefore parameter determines which of the arrays
     *                              the value will be assigned to.
     *
     *               <br>Note: Original indexes will not be preserved in the resulting arrays.
     *
     */
    public static function splitArrayAtValue(array $array, $value, $splitBefore = false): array
    {
        $valuesKey = ArrayUtility::getValuesKey($value, $array);
        // if value's key cannot be determined, log error and return an empty array.
        if ($valuesKey === false) {
            error_log(sprintf('ArrayUtility Implementation Error: Specified value "%s" does not exist.', serialize($value)));
            return array();
        }
        $preArray = array();
        $postArray = array();
        foreach ($array as $key => $val) {
            switch ($splitBefore) {
                case true: // BEFORE
                    if ($key < $valuesKey) {
                        array_push($preArray, $val);
                    }
                    if ($key >= $valuesKey) {
                        array_push($postArray, $val);
                    }
                    break;
                default: // AFTER
                    if ($key <= $valuesKey) {
                        array_push($preArray, $val);
                    }
                    if ($key > $valuesKey) {
                        array_push($postArray, $val);
                    }
                    break;
            }
        }
        return [$preArray, $postArray];
    }

}
