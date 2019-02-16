<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-01-01
 * Time: 22:44
 */

namespace DarlingCms\classes\staticClasses\core;


use DarlingCms\classes\database\SQL\MySqlQuery;

/** @todo ! Before things get out of hand, split this up into more niche Core* classes, for example:*EOL*The various path methods, getSiteRootUrl(), etc, could be placed in a class called CorePaths, which could implement the IPath interface. The MySqlQuery and database related methods could be placed in a factory class called CoreDBQuery...
 * Class CoreValues. This class defines various static methods which can be used to retrieve core values
 * such as the site's root url, root directory path, etc., more conveniently. This class also provides
 * methods for retrieving instances of commonly used objects, such as the core MySqlQuery instance used
 * to interact with the site's database(s).
 *
 * @devNote:
 * This class should only define methods for values that MUST be accessible to all Darling Cms Apps,
 * and to core. A value is considered a MUST only if it is expected to be used frequently enough in app
 * development to justify defining a centralized value for the sake of continuity. A good example of a
 * value that meets the "common" requirement the CoreValues::getSiteRootUrl() method which returns the
 * site's root url. The CoreValues::getSiteRootUrl() method meets the "common" requirement because it
 * is safe to assume that most apps will need to determine and use the site's root url, instead of each
 * app defining it's own way of determining the site's root url, it is more practical to provide that
 * value from a single method insuring continuity and making it easier to refactor the value if necessary
 * in the future.
 *
 * WARNING:
 * Careful consideration should be taken before adding new methods to this class, especially in regards
 * to security as this class creates a centralized global state via it's static methods.
 *
 * @package DarlingCms\classes\staticClasses\core
 * @todo ! many of these values should be read from a config source so permissible values can be set/changed, and so values that require care can be kept secure
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

    /**
     * @var MySqlQuery $MySqlQuery MySqlQuery implementation instance.
     */
    private static $MySqlQuery;

    /**
     * Returns the core MySqlQuery implementation instance.
     *
     * WARNING:
     * It is recommended this static method be used to avoid multiple database connections from being opened by
     * instantiating multiple MySqlQuery instances. Using this method insures that only one database connection
     * will be opened as only the single Core MySqlQuery instance will be used.
     *
     * @param string $databaseName The name of the database this MySqlQuery implementation instance will query. MUST be
     *                          one of the following constants:
     *
     *                          CORE_DB_NAME (default if provided $databaseName is not valid)
     *
     *                          APPS_DB_NAME
     *
     *                          USERS_DB_NAME
     *
     *                          PRIVILEGES_DB_NAME
     *
     * @param string $dbUser The username of the user this MySqlQuery instance will act as when querying the database.
     * @param string $dbPass The user's password.
     * @param string $charset (optional) The charset. Defaults to MySqlQuery::DEFAULT_CHARSET.
     * @return MySqlQuery The core MySqlQuery implementation instance.
     * @see MySqlQuery::DEFAULT_CHARSET
     * @see CoreValues::CORE_DB_NAME
     * @see CoreValues::APPS_DB_NAME
     * @see CoreValues::USERS_DB_NAME
     * @see CoreValues::PRIVILEGES_DB_NAME
     */
    public static function getMySqlQueryInstance(string $host, string $databaseName, string $dbUser, string $dbPass, string $charset = MySqlQuery::DEFAULT_CHARSET): MySqlQuery
    {
        $dbWhitelist = array(self::CORE_DB_NAME, self::APPS_DB_NAME, self::USERS_DB_NAME, self::PRIVILEGES_DB_NAME);
        if (in_array($databaseName, $dbWhitelist, true) === false) {
            $databaseName = self::CORE_DB_NAME;
        }
        if (isset(CoreValues::$MySqlQuery) === false) {
            CoreValues::$MySqlQuery = new MySqlQuery(MySqlQuery::getDsn($host, $databaseName, $charset), $dbUser, $dbPass);
        }
        return CoreValues::$MySqlQuery; // @todo ! This method needs refactoring, at the moment, if a new MySqlQuery instance is only created if one has not already been assigned to the $MySqlQuery property. This, as intended, prevents duplicate connections to a single database, but will also prevent the use of multiple databases.*EOL*For example:*EOL*App 1 starts up and calls getMySqlQueryInstance() to get a connection to the database matching CORE_DB_NAME*EOL*App B starts up and calls getMySqlQueryInstance() to get a connection to the database matching the APPS_DB_NAME*EOL*...in this context the getMySqlQueryInstance() method will still return a connection to the CORE_DB_NAME database since new instances are only created IF the $MySqlQuery property has not been set.*EOL*A fix for this that still protects against duplicate connections, but allows for connection to any of the whitelisted databases, MUST be found.
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
