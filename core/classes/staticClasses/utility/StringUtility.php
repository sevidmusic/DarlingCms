<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-01-19
 * Time: 15:24
 */

namespace DarlingCms\classes\staticClasses\utility;

/**
 * Class StringUtility. This class provides a collection of static utility methods for strings.
 *
 * Methods defined in this class MUST only:
 * - Modify strings
 * - Analyze strings
 *
 * Methods in this class MUST only return:
 * - Strings
 * - Booleans
 * @package DarlingCms\classes\staticClasses\utility
 */
class StringUtility
{
    public static function convertFromCamelCase(string $string): string
    {
        // Both REGEX solutions found on stackoverflow. @see https://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
        $pattern = '/(?(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z]))/x'; // ridgerunner's answer | BETTER: This pattern can accommodate even malformed camel case like camelCASEString
        //$pattern = '/((?:^|[A-Z])[a-z]+)/'; // codaddict's answer | approved answer | WARNING: This pattern does not handle malformed camel case strings like camelCASEString, kept for reference.
        $words = preg_split($pattern, $string);
        return ucwords(implode(' ', $words));
    }
}
