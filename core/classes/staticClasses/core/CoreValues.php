<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-01-01
 * Time: 22:44
 */

namespace DarlingCms\classes\staticClasses\core;

/**
 * Class CoreValues. This class defines various static methods which can be used to retrieve core values
 * such as the site's root url, root directory path, etc., more conveniently.
 *
 * @devNote:
 * This class MUST only define methods for values that MUST be accessible to all Darling Cms Apps,
 * and to core. A value is considered a MUST only if it is expected to be used frequently enough in app
 * development to justify defining a centralized value for the sake of continuity. A good example of a
 * value that meets the "common" requirement is site root url, which can be retrieved via the
 * CoreValues::getSiteRootUrl() method. The CoreValues::getSiteRootUrl() method meets the "common"
 * requirement because it is safe to assume that most apps will need to determine and use the site's
 * root url, instead of each app defining it's own way of determining the site's root url, it is more
 * practical to provide that value from a single method insuring continuity and making it easier to
 * refactor the value if necessary in the future.
 *
 * @devNote:
 * Only "simple" values should be returned by the methods in this class, i.e., only the following types
 * should be returned: bool, string, int, float
 *
 * More "complex" types, such as objects, arrays, etc., MUST not be returned by the methods defined in
 * this class.
 *
 * WARNING:
 * Careful consideration should be taken before adding new methods to this class, especially in regards
 * to security as this class creates a centralized global state via it's static methods.
 *
 * @package DarlingCms\classes\staticClasses\core
 * @todo ! many of these values should be read from a config source so permissible values can be set/changed via the relevant config file, and so values that require care can be kept more secure
 */
class CoreValues
{
    /**
     * Returns the name of the database used by Core.
     * @return string The name of the database used by Core.
     */
    public static function getCoreDBName(): string
    {
        return 'PDOPlaygroundDev1';
    }

    /**
     * Returns the name of the database used by Apps.
     * @return string The name of the database used by Apps.
     */
    public static function getAppsDBName(): string
    {
        return 'PDOPlaygroundDev1';
    }

    /**
     * Returns the name of the database used by Users, i.e., the name of the database
     * used to store users.
     * @return string The name of the database used by Users.
     */
    public static function getUsersDBName(): string
    {
        return 'PDOPlaygroundDev1';
    }


    /**
     * Returns the name of the database used by Passwords, i.e., the name of the database
     * used to store passwords.
     * @return string The name of the database used by Passwords.
     */
    public static function getPasswordsDBName(): string
    {
        return 'PDOPlaygroundDev1';
    }

    /**
     * Returns the name of the database used by Privileges, i.e., the name of the database
     * used to store Actions, Permissions, and Roles.
     * @return string The name of the database used by Privileges.
     */
    public static function getPrivilegesDBName(): string
    {
        return 'PDOPlaygroundDev1';
    }

    /**
     * Returns the name of the database host.
     * @return string The name of the database host.
     */
    public static function getDBHostName(): string
    {
        return 'localhost';
    }

    /**
     * Returns the database user's username.
     * @return string The database user's username.
     */
    public static function getDBUserName(): string
    {
        return 'root';
    }

    /**
     * Returns the database user's password.
     * @return string The database user's password.
     */
    public static function getDBPassword(): string
    {
        return 'root';
    }

    /**
     * Returns the site's root url.
     * @return string The site's root url.
     */
    public static function getSiteRootUrl(): string
    {
        $rootUrlPieces = parse_url((!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $rootUrl = $rootUrlPieces['scheme'] . '://' . $rootUrlPieces['host'] . (!empty($rootUrlPieces['port']) ? ':' . $rootUrlPieces['port'] : '') . $rootUrlPieces['path'];
        $replace = substr($rootUrl, strrpos($rootUrl, '/'));
        $siteRootUrl = ($replace !== '/' ? str_replace($replace, '', $rootUrl) . '/' : $rootUrl);
        return $siteRootUrl;
    }


    /**
     * Returns the path to the site's apps directory.
     * @return string The path to the site's apps directory.
     */
    public static function getAppsRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/apps';
    }

    /**
     * Returns the path to the site's root directory.
     * @return string The path to the site's root directory.
     */
    public static function getSiteRootDirPath(): string
    {
        return str_replace('/core/classes/staticClasses/core', '', __DIR__);
    }

    /**
     * Returns the path to the specified app's directory.
     * @param string $appName The name of the app whose path should be returned.
     * @return string The path to the specified app's directory.
     */
    public static function getAppDirPath(string $appName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $appName;
    }

    /**
     * Returns the path to the site's javascript library directory.
     * @return string The path to the site's javascript library directory.
     */
    public static function getJsLibRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/js';
    }

    /**
     * Returns the path to the specified javascript library's directory.
     * @param string $jsLibraryName The name of the javascript library whose path should be returned.
     * @return string The path to the specified app's directory.
     */
    public static function getJsLibDirPath(string $jsLibraryName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $jsLibraryName;
    }

    /**
     * Returns the path to the site's themes directory.
     * @return string The path to the site's themes directory.
     */
    public static function getThemesRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/apps';
    }

    /**
     * Returns the path to the specified theme's directory.
     * @param string $themeName The name of the theme whose path should be returned.
     * @return string The path to the specified theme's directory.
     */
    public static function getThemeDirPath(string $themeName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $themeName;
    }

}
