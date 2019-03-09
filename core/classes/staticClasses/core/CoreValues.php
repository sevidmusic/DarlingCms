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
 * and to core. A value is considered a MUST only if it is expected to be used frequently enough in
 * app development or in Core to justify defining a centralized value for the sake of continuity.
 *
 * A good example of a value that meets this requirement is the site root url, which can be
 * retrieved via the CoreValues::getSiteRootUrl() method. The CoreValues::getSiteRootUrl() method
 * meets the this requirement because it is safe to assume that most apps, and core will need to
 * determine and use the site's root url, instead of core and every app defining their own way of
 * determining the site's root url, it is more practical to provide that value from a single method
 * insuring continuity, and making it easier to refactor the value if necessary in the future.
 *
 * @devNote:
 * Only "simple" values should be returned by the "public" static methods in this class, i.e., only the following
 * types should be returned: bool, string, int, float
 *
 * More "complex" types, such as objects, arrays, etc., MUST not be returned by the public static methods
 * defined in this class. For complex return types define appropriately named private static methods that
 * can be accessed via one of the public static methods.
 *
 * WARNING:
 * Careful consideration should be taken before adding new methods to this class, especially in regards
 * to security as this class creates a centralized global state via it's public static methods.
 *
 * @package DarlingCms\classes\staticClasses\core
 */
class CoreValues
{
    /**
     * @var string Name of the setting that determines the name of the database used by Core.
     */
    const CORE_DB_NAME_SETTING = "CoreDBName";

    /**
     * @var string Name of the setting that determines the name of the database used by Apps.
     */
    const APPS_DB_NAME_SETTING = "AppsDBName";

    /**
     * @var string Name of the setting that determines the name of the database used to store User data.
     */
    const USERS_DB_NAME_SETTING = "UsersDBName";

    /**
     * @var string Name of the setting that determines the name of the database used to store User passwords.
     */
    const PASSWORD_DB_NAME_SETTING = "PasswordsDBName";

    /**
     * @var string Name of the setting that determines the name of the database where privilege data is stored, i.e., Actions, Permissions, Roles.
     */
    const PRIVILEGES_DB_NAME_SETTING = "PrivilegesDBName";

    /**
     * @var string Name of the setting that determines the name of the database host. @todo Need to allow different config in case of databases being different as login info may also be different, I.E. AppsDbHost, CoreDbHost, PrivilegesDbHost, etc...
     */
    const DB_HOST_NAME_SETTING = "DBHostName";

    /**
     * @var string Name of the setting that determines the name of the database user. @todo need to allow for different config if multiple db users are needed, i.e., in the case of multiple databases where each database requires different authentication.
     */
    const DB_USER_NAME_SETTING = "DBUserName";

    /**
     * @var string Name of the setting that determines the database user's password. @todo need to allow for different config if multiple db user passwords are needed, i.e., in the case of multiple databases where each database requires different authentication.
     */
    const DB_PASSWORD_SETTING = "DBPassword";

    /**
     * @var string Default site url name. Used when CoreValues::getSiteUrlName() is unable to determine
     *             site url name.
     */
    const DEFAULT_SITE_URL_NAME = 'DCMS_DEFAULT_SITE_NAME';

    /**
     * @var string Name of the directory where site configuration files are stored.
     */
    const SITE_CONFIG_DIR_NAME = '.dcms';

    /**
     * @var string Extension used for Darling Cms site configuration files.
     */
    const SITE_CONFIG_EXT = '.config.ini';

    /**
     * Returns an array of the site's configuration settings as defined in the site's configuration file.
     * Note: This method will return an empty array if the site's configuration file cannot be found, or
     * if the site's configuration file does not define any configuration settings.
     * @return array An array of the site's configuration settings as defined in the site's configuration
     *               file, or an empty array if the site's configuration file cannot be found, or if the
     *               site's configuration file does not define any configuration settings.
     */
    private static function getSiteConfigArray(): array
    {
        $config = [];
        if (file_exists(self::getSiteConfigPath()) === true) {
            $config = parse_ini_file(self::getSiteConfigPath());
            if (empty($config) === true) {
                error_log('Darling Cms Core Error: A valid configuration file for the site could not be found, please define one.');
            }
        }
        return (is_array($config) === true && empty($config) === false ? $config : []);
    }

    /**
     * Returns the path to the site's configuration file.
     *
     * Note: This includes the site configuration file's name.
     *
     * e.g.,
     *
     * /path/to/config/dir/siteConfigName.config.ini
     *
     * @return string The path to the site's configuration file.
     */
    public static function getSiteConfigPath(): string
    {
        return str_replace(self::getSiteDirName(), self::SITE_CONFIG_DIR_NAME . '/' . self::getSiteConfigFilename(), self::getSiteRootDirPath());
    }

    /**
     * Returns the name of the site's configuration file.
     *
     * Note: The name of the site's configuration file is determined by concatenating the string returned
     * by the CoreValues::getSiteUrlName() method and value of the CoreValues::SITE_CONFIG_EXT constant.
     * @return string The name of the site's configuration file.
     */
    public static function getSiteConfigFilename()
    {
        return self::getSiteUrlName() . self::SITE_CONFIG_EXT;
    }

    /**
     * Returns the value of the specified configuration setting.
     * @param string $settingName The name of the setting whose value should be returned.
     *
     * Note: This class defines the following constants that can be used to identify required site
     * configuration settings:
     *
     * CoreValues::CORE_DB_NAME_SETTING Name of the setting that determines the name of the database used by Core.
     *
     * CoreValues::APPS_DB_NAME_SETTING Name of the setting that determines the name of the database used by Apps.
     *
     * CoreValues::USERS_DB_NAME_SETTING Name of the setting that determines the name of the database used to store User data.
     *
     * CoreValues::PASSWORD_DB_NAME_SETTING Name of the setting that determines the name of the database used to store User passwords.
     *
     * CoreValues::PRIVILEGES_DB_NAME_SETTING Name of the setting that determines the name of the database where privilege data is stored, i.e., Actions, Permissions, Roles.
     *
     * CoreValues::DB_HOST_NAME_SETTING Name of the setting that determines the name of the database host.
     *
     * CoreValues::DB_USER_NAME_SETTING Name of the setting that determines the name of the database user.
     *
     * CoreValues::DB_PASSWORD_SETTING Name of the setting that determines the database user's password.
     *
     * @return mixed|null The settings value, or null. Note: Some setting may be defined as null, so it
     *                    is not reliable to check if this methods return value is null to determine
     *                    whether or not the specified setting exists and is set.
     *
     * @see CoreValues::CORE_DB_NAME_SETTING
     * @see CoreValues::APPS_DB_NAME_SETTING
     * @see CoreValues::USERS_DB_NAME_SETTING
     * @see CoreValues::PASSWORD_DB_NAME_SETTING
     * @see CoreValues::PRIVILEGES_DB_NAME_SETTING
     * @see CoreValues::DB_HOST_NAME_SETTING
     * @see CoreValues::DB_USER_NAME_SETTING
     * @see CoreValues::DB_PASSWORD_SETTING
     */
    public static function getSiteConfigValue(string $settingName)
    {
        $config = self::getSiteConfigArray();
        return (isset($config[$settingName]) === true ? $config[$settingName] : null);
    }

    /**
     * Determines whether or not the site has been configured, i.e., whether or not this
     * is a fresh installation of the Darling Cms.
     * @return bool True if site has been configured, false otherwise.
     */
    public static function siteConfigured(): bool
    {
        return (!empty(self::getSiteConfigArray()) === true && self::verifyCoreSettings() === true);
    }

    /**
     * Verifies if the site configuration contains all the required settings.
     * @return bool True if site configuration has all required settings, false otherwise.
     */
    private static function verifyCoreSettings()
    {
        $status = array();
        $config = self::getSiteConfigArray();
        $requiredSettings = array(
            self::CORE_DB_NAME_SETTING,
            self::APPS_DB_NAME_SETTING,
            self::USERS_DB_NAME_SETTING,
            self::PASSWORD_DB_NAME_SETTING,
            self::PRIVILEGES_DB_NAME_SETTING,
            self::DB_HOST_NAME_SETTING,
            self::DB_USER_NAME_SETTING,
            self::DB_PASSWORD_SETTING,
        );
        foreach ($requiredSettings as $requiredSetting) {
            array_push($status, in_array($requiredSetting, array_keys($config), true));
        }
        return !in_array(false, $status, true);
    }

    /**
     * Returns the site's directory name.
     * @return string The site's directory name.
     */
    public static function getSiteDirName(): string
    {
        return pathinfo(self::getSiteRootDirPath(), PATHINFO_BASENAME);
    }


    /**
     * Return's the site name derived from the site's root url.
     *
     * For example:
     *
     * For a site whose root url is "http://www.example.com" this method would
     * return "example.com".
     *
     * Note: To get the site's complete root url use the CoreValues::getSiteRootUrl() method.
     *
     * WARNING: This method utilizes PHP's parse_url() function to derive the site's name from the
     * site root url, if parse_url() fails then the value of the CoreValues::DEFAULT_SITE_URL_NAME
     * constant will be returned.
     *
     * Note: This method will try to accommodate a local environment. For instance:
     * On a local environment the root url may look something like "http://localhost/example.dev"
     * or "http://localhost:8888/example.dev" in which case this method will attempt to derive
     * the site url name from the path, which would return "example.dev" if successful. If the
     * path cannot be used for some reason, then either "localhost" or the default site url name
     * defined by the CoreValues::::DEFAULT_SITE_URL_NAME will be returned. This only applies to
     * a local environment.
     *
     * @return string The site name derived from the site's root url.
     * @see CoreValues::getSiteRootUrl()
     */
    public static function getSiteUrlName(): string
    {
        $parsedUrl = parse_url(self::getSiteRootUrl());
        // If 'host' is not set, assume parse_url() failed and return a default value
        if (empty($parsedUrl['host']) === true) {
            return self::DEFAULT_SITE_URL_NAME;
        }
        /**
         * @var array Array of values that should always be excluded from the site url name.
         */
        $replace = array('/', 'www.', '.php', '.html');
        /**
         * For localhost use 'path', this is necessary b/c on a local environment the site will typically
         * follow a format such as "http://localhost/example.dev/foo" or "http://localhost:8888/example.dev/foo"
         * and for such a url parse_url will return "localhost" as the 'host', not "example.dev".
         *
         * Note: If parse_url() failed to determine path, i.e. path is not set or is empty, then
         *       proceed using host, which for local environment will most likely be "localhost".
         */
        if ($parsedUrl['host'] === 'localhost' && empty($parsedUrl['path']) === false && $parsedUrl['path'] !== '/') {
            /**
             * Filter the path so only the chars following the first / are used and chars following any
             * subsequent / are excluded, i.e. if the path is /example.dev/foo/bar/baz exclude /foo/bar/baz
             */
            $localPath = explode('/', $parsedUrl['path'])[1];
            return str_replace($replace, '', $localPath);
        }
        return str_replace($replace, '', $parsedUrl['host']);
    }

    /**
     * Returns the name of the database used by Core.
     * @return string The name of the database used by Core.
     */
    public static function getCoreDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::CORE_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::CORE_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used by Apps.
     * @return string The name of the database used by Apps.
     */
    public static function getAppsDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::APPS_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::APPS_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used by Users, i.e., the name of the database
     * used to store Users.
     * @return string The name of the database used to store Users.
     */
    public static function getUsersDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::USERS_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::USERS_DB_NAME_SETTING) : '');
    }


    /**
     * Returns the name of the database used to store Passwords.
     * @return string The name of the database used to store Passwords.
     */
    public static function getPasswordsDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::PASSWORD_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PASSWORD_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used to store Privileges, i.e., the name of the database
     * used to store Actions, Permissions, and Roles.
     * @return string The name of the database used to store Privileges.
     */
    public static function getPrivilegesDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::PRIVILEGES_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PRIVILEGES_DB_NAME_SETTING) : '');

    }

    /**
     * Returns the name of the database host.
     * @return string The name of the database host.
     */
    public static function getDBHostName(): string
    {
        return (empty(self::getSiteConfigValue(self::DB_HOST_NAME_SETTING)) === false ? self::getSiteConfigValue(self::DB_HOST_NAME_SETTING) : '');
    }

    /**
     * Returns the database user's username.
     * @return string The database user's username.
     */
    public static function getDBUserName(): string
    {
        return (empty(self::getSiteConfigValue(self::DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::DB_USER_NAME_SETTING) : '');

    }

    /**
     * Returns the database user's password.
     * @return string The database user's password.
     */
    public static function getDBPassword(): string
    {
        return (empty(self::getSiteConfigValue(self::DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::DB_PASSWORD_SETTING) : '');

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
     * Returns the path to the site's root directory.
     * @return string The path to the site's root directory.
     */
    public static function getSiteRootDirPath(): string
    {
        return str_replace('/core/classes/staticClasses/core', '', __DIR__);
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
     * Returns the path to the specified app's directory.
     * @param string $appName The name of the app whose path should be returned.
     * @return string The path to the specified app's directory.
     */
    public static function getAppDirPath(string $appName): string
    {
        return self::getAppsRootDirPath() . '/' . $appName;
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
     * @return string The path to the specified Js Library's directory.
     */
    public static function getJsLibDirPath(string $jsLibraryName): string
    {
        return self::getJsLibRootDirPath() . '/' . $jsLibraryName;
    }

    /**
     * Returns the path to the site's themes directory.
     * @return string The path to the site's themes directory.
     */
    public static function getThemesRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/themes';
    }

    /**
     * Returns the path to the specified theme's directory.
     * @param string $themeName The name of the theme whose path should be returned.
     * @return string The path to the specified theme's directory.
     */
    public static function getThemeDirPath(string $themeName): string
    {
        return self::getThemesRootDirPath() . '/' . $themeName;
    }

}
