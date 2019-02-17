<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-01-01
 * Time: 22:44
 */

namespace DarlingCms\classes\staticClasses\core;


use DarlingCms\classes\database\SQL\MySqlQuery;

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
    // @todo ! *ACTIVE* Since constants cannot be assigned a value dynamically some of the current class constants may need to be refactored into properties that can be retrieved by corresponding get*() methods
    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database used by Core.
     */
    const CORE_DB_NAME = 'PDOPlaygroundDev1';

    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database used by Apps.
     */
    const APPS_DB_NAME = 'PDOPlaygroundDev1';

    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database used to store Users.
     */
    const USERS_DB_NAME = 'PDOPlaygroundDev1';

    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database used to store Users.
     */
    const PASSWORD_DB_NAME = 'PDOPlaygroundDev1';

    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database used to store Privileges, i.e., Actions, Permissions, and Roles.
     */
    const PRIVILEGES_DB_NAME = 'PDOPlaygroundDev1';

    /** @todo ! *ACTIVE* Read from config file | Implement support for custom database names defined via config file
     * @var string Name of the database used to store Privileges, i.e., Actions, Permissions, and Roles.
     */
    //const CUSTOM_DB_NAMES = [];

    /** @todo ! *ACTIVE* Read from config file
     * @var string Name of the database host, e.g., localhost
     */
    const CORE_DB_HOST = 'localhost';

    const CORE_DB_USER = 'root'; // @todo ! This const is for dev only, live env must use a config file to obtain this value

    const CORE_DB_PASS = 'root'; // @todo ! This const is for dev only, live env must use a config file to obtain this value

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
