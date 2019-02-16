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
 * such as the site's root url, root directory path, etc.
 * This class should only define methods for values that MUST be accessible to all Darling Cms Apps, and to core.
 *
 * WARNING: Careful consideration should be taken before adding new methods to this class, especially in regards
 *          to security as this class creates a centralized global state via it's static methods.
 * @package DarlingCms\classes\staticClasses\core
 * @todo ! many of these values should be read from a config source so permissible values can be set/changed, and so values that require care can be kept secure
 */
class CoreValues
{
    const CORE_DB_NAME = 'PDOPlaygroundDev1';
    const APPS_DB_NAME = 'PDOPlaygroundDev1';
    const USERS_DB_NAME = 'PDOPlaygroundDev1';
    const PRIVILEGES_DB_NAME = 'PDOPlaygroundDev1';
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
        return CoreValues::$MySqlQuery;
    }

    public static function getSiteRootUrl(): string
    {
        $rootUrlPieces = parse_url((!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $rootUrl = $rootUrlPieces['scheme'] . '://' . $rootUrlPieces['host'] . (!empty($rootUrlPieces['port']) ? ':' . $rootUrlPieces['port'] : '') . $rootUrlPieces['path'];
        $replace = substr($rootUrl, strrpos($rootUrl, '/'));
        $siteRootUrl = ($replace !== '/' ? str_replace($replace, '', $rootUrl) . '/' : $rootUrl);
        return $siteRootUrl;
    }

    public static function getAppsRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/apps';
    }

    public static function getSiteRootDirPath(): string
    {
        return str_replace('/core/classes/staticClasses/core', '', __DIR__);
    }

    public static function getAppDirPath(string $appName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $appName;
    }

    public static function getJsLibRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/js';
    }

    public static function getJsLibDirPath(string $jsLibraryName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $jsLibraryName;
    }

    public static function getThemesRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/apps';
    }

    public static function getThemeDirPath(string $themeName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $themeName;
    }

}
