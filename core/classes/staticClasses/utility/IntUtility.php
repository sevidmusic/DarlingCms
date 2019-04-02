<?php


namespace DarlingCms\classes\staticClasses\utility;

/**
 * Class IntUtility. This class provides a collection of static utility methods for integers.
 *
 * @devNote:
 *
 * Methods defined in this class MUST only:
 * - Modify integers
 * - Analyze integers
 *
 * Methods in this class MUST only return the following types:
 * - int
 * - bool
 * @package DarlingCms\classes\staticClasses\utility
 */
class IntUtility
{
    /**
     * Determines if an integer is in fact an octal number.
     * @param int $int The integer to test.
     * @return bool True if int is in fact an octal, false otherwise.
     */
    public static function intIsOctal(int $int): bool
    {
        return intval(decoct(octdec($int))) === $int;
    }
}
