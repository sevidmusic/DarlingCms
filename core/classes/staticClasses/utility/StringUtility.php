<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-01-19
 * Time: 15:24
 */

namespace DarlingCms\classes\staticClasses\utility;

use Exception;

/**
 * Class StringUtility. This class provides a collection of static utility methods for strings.
 *
 * @devNote:
 *
 * Methods defined in this class MUST only:
 * - Modify strings
 * - Analyze strings
 *
 * Methods in this class MUST only return the following types:
 * - string
 * - bool
 *
 * @package DarlingCms\classes\staticClasses\utility
 *
 * @see StringUtility::convertFromCamelCase()
 * @see StringUtility::randString()
 * @see StringUtility::filterAlphaNumeric()
 */
class StringUtility
{
    /**
     * Converts a camel case string to words.
     *
     * For example:
     *
     * StringUtility::convertFromCamelCase('CamelCaseString'); // returns "Camel Case String"
     *
     * @param string $string The string to covert.
     *
     * @return string The converted string.
     */
    public static function convertFromCamelCase(string $string): string
    {
        // Both REGEX solutions found on stackoverflow. @see https://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
        $pattern = '/(?(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z]))/x'; // ridgerunner's answer | BETTER: This pattern can accommodate even malformed camel case like camelCASEString
        //$pattern = '/((?:^|[A-Z])[a-z]+)/'; // codaddict's answer | approved answer | WARNING: This pattern does not handle malformed camel case strings like camelCASEString, kept for reference.
        $words = preg_split($pattern, $string);
        return ucwords(implode(' ', $words));
    }

    /**
     * Generates a random string of characters between 16 and 32 in length.
     *
     * Note: This method will attempt to generate a random string that is cryptographically secure,
     * if it is unable to do so it will log an error and return a random string that is NOT
     * cryptographically secure.
     *
     * Note: The length of the string is also random, but will always be between 16 and 32 in length.
     *
     * @return string The random string of characters.
     */
    public static function randString(): string
    {
        try {
            $length = rand(32, 64); // generated string should be between 16 and 32 chars.
            return bin2hex(random_bytes(($length - ($length % 2)) / 2));
        } catch (Exception $e) {
            error_log('String Utility Error: Could not generate cryptographically secure random string.');
        }
        return substr(StringUtility::filterAlphaNumeric(base64_encode(strval(rand(PHP_INT_MIN, PHP_INT_MAX)) . strval(rand(PHP_INT_MIN, PHP_INT_MAX)) . strval(rand(PHP_INT_MIN, PHP_INT_MAX)))), 0, rand(16, 32));
    }

    /**
     * Removes all non-alpha-numeric characters from a string.
     *
     * Note: This method will also remove all white space unless the $preserveWhiteSpace
     *       parameter is explicitly set to true.
     *
     * @param string $string The string to filter.
     *
     * @param bool $preserveWhiteSpace If set to true, then whitespace will be preserved, if set
     *                                 to false whitespace will be removed, defaults to false.
     *
     * @return string The filtered string.
     */
    public static function filterAlphaNumeric(string $string, $preserveWhiteSpace = false): string
    {
        if ($preserveWhiteSpace === true) {
            return trim(preg_replace("/[^a-zA-Z0-9\s]+/", "", $string));
        }
        return trim(preg_replace("/[^a-zA-Z0-9]+/", "", $string));
    }
}
