<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-01-01
 * Time: 22:44
 */

namespace DarlingCms\classes\staticClasses\core;


use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\interfaces\database\SQL\ISQLQuery;

/**
 * Class CoreValues. This class defines various static methods which can be used to retrieve core values
 * such as the site's root url, root directory path, etc.
 * This class should only define methods for values that MUST be accessible to all Darling Cms Apps, and to core.
 *
 * WARNING: Careful consideration should be taken before adding new methods to this class, especially in regards
 *          to security as this class creates a centralized global state via it's static methods.
 * @package DarlingCms\classes\staticClasses\core
 * @todo many of these values should be read from a config source so permissible values can be set/changed
 */
class CoreValues
{
    private static $dev;

    const CORE_DB_NAME = 'PDOPlaygroundDev1';
    const CORE_DB_HOST = 'localhost';


    /**
     * Returns the core ISqlQuery implementation instance. It is recommended this static method be used
     *
     * @param int $databaseName The name of the database this ISqlQuery implementation instance will query. MUST be
     *                          one of the following constants:
     *                          CORE_DB_NAME
     *                          APPS_DB_NAME
     *                          USERS_DB_NAME
     *                          PRIVILEGES_DB_NAME
     * @param string $dbUser The username of the user this ISqlQuery instance will act as when querying the database.
     * @param string $dbPass Th user's password.
     * @param string $charset (optional) The charset. Defaults to MySqlQuery::DEFAULT_CHARSET.
     * @return ISQLQuery The core ISqlQuery implementation instance.
     */
    public static function getISqlQueryInstance(string $host, string $databaseName, string $dbUser, string $dbPass, string $charset = MySqlQuery::DEFAULT_CHARSET): ISQLQuery
    {
        if (isset(CoreValues::$dev) === false) {
            CoreValues::$dev = new MySqlQuery(MySqlQuery::getDsn($host, $databaseName, $charset), $dbUser, $dbPass);
        }
        return CoreValues::$dev;
    }

    public static function getSiteRootDirPath(): string
    {
        return str_replace('/core/classes/staticClasses/core', '', __DIR__);
    }

    public static function getSiteRootUrl(): string
    {
        return 'http://localhost:8888/DarlingCms';
    }

    public static function getAppsRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/apps';
    }

    public static function getAppDirPath(string $appName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $appName;
    }

    public static function getJsLibRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/js';
    }

    public static function getJsLibDirPath(string $appName): string
    {
        return self::getSiteRootDirPath() . '/apps/' . $appName;
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
