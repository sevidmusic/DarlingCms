<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-01-01
 * Time: 22:44
 */

namespace DarlingCms\classes\staticClasses\core;

/**
 * Class CoreValues. This class defines various static methods which can be
 * used to retrieve Core values such as the site's root url, root directory
 * path, site configuration settings, etc., more conveniently.
 *
 * @devNote:
 * This class MUST only define methods for values that MUST be accessible to
 * all Darling Cms Apps, and to Core. A value is considered a MUST only if it
 * is expected to be used frequently enough in app development or in Core to
 * justify defining a centralized value for the sake of continuity.
 *
 * A good example of a value that meets this requirement is the site root url,
 * which can be retrieved via the CoreValues::getSiteRootUrl() method. The
 * CoreValues::getSiteRootUrl() method meets the this requirement because it
 * is safe to assume that most apps, and Core will need to determine and use
 * the site's root url. Instead of Core and every app defining their own way of
 * determining the site's root url, it is more practical to provide that value
 * from a single method insuring continuity, and making it easier to refactor
 * the value if necessary in the future.
 *
 * @devNote:
 * Only "simple" values should be returned by the "public" static methods in
 * this class, i.e., only the following types should be returned:
 * - bool
 * - string
 * - int
 * - float
 *
 * More "complex" types, such as objects, arrays, etc., MUST not be returned by
 * the public static methods defined in this class. For complex return types
 * define appropriately named private static methods that can be accessed via
 * one of the public static methods to return a simple type.
 *
 * For example:
 *
 * private static function Foo():array {
 *     // ...code...
 * }
 *
 * public static function Bar($key):string {
 *     return (
 *             empty(Foo()[$key]) === false
 *             && is_string(Foo()[$key]) === true
 *             ? Foo()[$key]
 *             : ''
 *             );
 * }
 *
 * Again, CoreValues public static methods MUST only return the following types:
 * - bool
 * - string
 * - int
 * - float
 *
 * Note: There is one exception to the rule above, the getRequiredSettingNames()
 *       method returns an array. This method has been grandfathered in as it
 *       is an important part of Core. This method may eventyally be moved
 *       to another class.
 *       Despite the exception described above, new public static methods
 *       added to this class MUST NOT return complex types.
 *
 * @todo: Define a CoreDataStructures class that is similar to CoreValues, but designed to return complex types, and move the getRequiredSettingNames() method listed above into that new class so that CoreValues is compliant with the rule that disallows complex types be returned from it's public static methods.
 *
 * WARNING:
 * Careful consideration should be taken before adding new methods to this class,
 * especially in regards to security as this class creates a centralized global
 * state via it's public static methods.
 *
 * @package DarlingCms\classes\staticClasses\core
 */
class CoreValues
{
    /**
     * @var string Name of the site configuration setting that defines the name of
     *             the database used by Core.
     */
    const CORE_DB_NAME_SETTING = "CoreDBName";

    /**
     * @var string Name of the site configuration setting that defines the name of
     *             host for the database used by Core.
     */
    const CORE_DB_HOST_SETTING = "CoreDBHost";

    /**
     * @var string Name of the site configuration setting that defines the Core database
     *             user's username.
     */
    const CORE_DB_USER_NAME_SETTING = "CoreDBUserName";

    /**
     * @var string Name of the site configuration setting that defines the Core database
     *             user's password.
     */
    const CORE_DB_PASSWORD_SETTING = "CoreDBPassword";


    /**
     * @var string Name of the site configuration setting that defines the name of the
     *             database used by Apps.
     */
    const APPS_DB_NAME_SETTING = "AppsDBName";

    /**
     * @var string Name of the site configuration setting that defines the name of host
     *             for the database used by Apps.
     */
    const APPS_DB_HOST_SETTING = "AppsDBHost";

    /**
     * @var string Name of the site configuration setting that defines the Apps database
     *             user's username.
     */
    const APPS_DB_USER_NAME_SETTING = "AppsDBUserName";

    /**
     * @var string Name of the site configuration setting that defines the Apps database
     *             user's password.
     */
    const APPS_DB_PASSWORD_SETTING = "AppsDBPassword";

    /**
     * @var string Name of the site configuration setting that defines the name of the
     *             database used to store User data.
     */
    const USERS_DB_NAME_SETTING = "UsersDBName";

    /**
     * @var string Name of the site configuration setting that defines the name of host
     *             for the database used to store User data.
     */
    const USERS_DB_HOST_SETTING = "UsersDBHost";

    /**
     * @var string Name of the site configuration setting that defines the Users database
     *             user's username.
     */
    const USERS_DB_USER_NAME_SETTING = "UsersDBUserName";

    /**
     * @var string Name of the site configuration setting that defines the Users database
     *             user's password.
     */
    const USERS_DB_PASSWORD_SETTING = "UsersDBPassword";

    /**
     * @var string Name of the site configuration setting that defines the name of the
     *             database used to store Password data.
     */
    const PASSWORDS_DB_NAME_SETTING = "PasswordsDBName";

    /**
     * @var string Name of the site configuration setting that defines the name of host
     *             for the database used to store Password data.
     */
    const PASSWORDS_DB_HOST_SETTING = "PasswordsDBHost";

    /**
     * @var string Name of the site configuration setting that defines the Passwords
     *             database user's username.
     */
    const PASSWORDS_DB_USER_NAME_SETTING = "PasswordsDBUserName";

    /**
     * @var string Name of the site configuration setting that defines the Passwords
     *             database user's password.
     */
    const PASSWORDS_DB_PASSWORD_SETTING = "PasswordsDBPassword";

    /**
     * @var string Name of the site configuration setting that defines the name of the
     *             database used to store Privileges, i.e., Actions, Permissions, Roles.
     */
    const PRIVILEGES_DB_NAME_SETTING = "PrivilegesDBName";

    /**
     * @var string Name of the site configuration setting that defines the name of host
     *             for the database used to store Privileges, i.e., Actions, Permissions,
     *             and Roles.
     */
    const PRIVILEGES_DB_HOST_SETTING = "PrivilegesDBHost";

    /**
     * @var string Name of the site configuration setting that defines the Privileges
     *             database user's username.
     */
    const PRIVILEGES_DB_USER_NAME_SETTING = "PrivilegesDBUserName";

    /**
     * @var string Name of the site configuration setting that defines the  Privileges
     *             database user's password.
     */
    const PRIVILEGES_DB_PASSWORD_SETTING = "PrivilegesDBPassword";

    /**
     * @var string Default site url name. Used when CoreValues::getSiteUrlName() is
     *             unable to determine site url name.
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
     * @var string Default value used by the CoreValues::getSiteRootUrl() method when
     *             it is unable to determine the Site's root url.
     */
    private const DEFAULT_SITE_ROOT_URL = '';

    /**
     * Returns an array of the site's configuration settings as defined in the site's
     * configuration file.
     *
     * Note: This method will return an empty array if the site's configuration file
     *       cannot be found, or if the site's configuration file does not define any
     *       configuration settings.
     *
     * @return array An array of the site's configuration settings as defined in the
     *               site's configuration file, or an empty array if the site's
     *               configuration file cannot be found, or if the site's configuration
     *               file does not define any configuration settings.
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
     * Note: The name of the site's configuration file is determined by concatenating
     *       the string returned by the CoreValues::getSiteUrlName() method and value
     *       of the CoreValues::SITE_CONFIG_EXT constant.
     *
     * @return string The name of the site's configuration file.
     *
     * @see CoreValues::getSiteUrlName()
     * @see CoreValues::SITE_CONFIG_EXT
     */
    public static function getSiteConfigFilename()
    {
        return self::getSiteUrlName() . self::SITE_CONFIG_EXT;
    }

    /**
     * Returns the value of the specified configuration setting, or an empty string.
     *
     * Note: Some settings may be defined as an empty string, so it is not reliable
     * to check if this method's return value is an empty string to determine whether
     * or not the specified setting exists and is set.
     *
     * @param string $settingName The name of the site configuration setting whose
     *                            value should be returned.
     *
     * Note: This class defines the following constants that can be used to identify
     *       required site configuration settings:
     *
     * - CoreValues::CORE_DB_NAME_SETTING : Name of the site configuration setting that
     *                                      defines the name of the database used by Core.
     *
     * - CoreValues::CORE_DB_HOST_SETTING : Name of the site configuration setting that
     *                                      defines the name of host for the database
     *                                      used by Core.
     *
     * - CoreValues::CORE_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the Core database user's
     *                                           username.
     *
     * - CoreValues::CORE_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                          that defines the Core database user's
     *                                          password.
     *
     * - CoreValues::APPS_DB_NAME_SETTING : Name of the site configuration setting that
     *                                      defines the name of the database used by Apps.
     *
     * - CoreValues::APPS_DB_HOST_SETTING : Name of the site configuration setting that
     *                                      defines the name of host for the database
     *                                      used by Apps.
     *
     * - CoreValues::APPS_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the Apps database user's
     *                                           username.
     *
     * - CoreValues::APPS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                          that defines the Apps database user's
     *                                          password.
     *
     * - CoreValues::USERS_DB_NAME_SETTING : Name of the site configuration setting that
     *                                       defines the name of the database used to
     *                                       store User data.
     *
     * - CoreValues::USERS_DB_HOST_SETTING : Name of the site configuration setting that
     *                                       defines the name of host for the database
     *                                       used to store User data.
     *
     * - CoreValues::USERS_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                            that defines the Users database user's
     *                                            username.
     *
     * - CoreValues::USERS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                           that defines the Users database user's
     *                                           password.
     *
     * - CoreValues::PASSWORDS_DB_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the name of the database
     *                                           used to store Password data.
     *
     * - CoreValues::PASSWORDS_DB_HOST_SETTING : Name of the site configuration setting
     *                                           that defines the name of host for the
     *                                           database used to store Password data.
     *
     * - CoreValues::PASSWORDS_DB_USER_NAME_SETTING : Name of the site configuration
     *                                                setting that defines the Passwords
     *                                                database user's username.
     *
     * - CoreValues::PASSWORDS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                               that defines the Passwords database
     *                                               user's password.
     *
     * - CoreValues::PRIVILEGES_DB_NAME_SETTING : Name of the site configuration setting
     *                                            that defines the name of the database
     *                                            used to store Privileges, i.e., Actions,
     *                                            Permissions, Roles.
     *
     * - CoreValues::PRIVILEGES_DB_HOST_SETTING : Name of the site configuration setting
     *                                            that defines the name of host for the
     *                                            database used to store Privileges,
     *                                            i.e., Actions, Permissions, and Roles.
     *
     * - CoreValues::PRIVILEGES_DB_USER_NAME_SETTING : Name of the site configuration
     *                                                 setting that defines the Privileges
     *                                                 database user's username.
     *
     * - CoreValues::PRIVILEGES_DB_PASSWORD_SETTING : Name of the site configuration
     *                                                setting that defines the Privileges
     *                                                database user's password.
     *
     * @return string The settings value, or an empty string. Note: Some settings may
     *                be defined as an empty string, so it is not reliable to check if
     *                this method's return value is an empty string to determine whether
     *                or not the specified setting exists and is set.
     *
     * @see CoreValues::CORE_DB_NAME_SETTING
     * @see CoreValues::CORE_DB_HOST_SETTING
     * @see CoreValues::CORE_DB_USER_NAME_SETTING
     * @see CoreValues::CORE_DB_PASSWORD_SETTING
     * @see CoreValues::APPS_DB_NAME_SETTING
     * @see CoreValues::APPS_DB_HOST_SETTING
     * @see CoreValues::APPS_DB_USER_NAME_SETTING
     * @see CoreValues::APPS_DB_PASSWORD_SETTING
     * @see CoreValues::USERS_DB_NAME_SETTING
     * @see CoreValues::USERS_DB_HOST_SETTING
     * @see CoreValues::USERS_DB_USER_NAME_SETTING
     * @see CoreValues::USERS_DB_PASSWORD_SETTING
     * @see CoreValues::PASSWORDS_DB_NAME_SETTING
     * @see CoreValues::PASSWORDS_DB_HOST_SETTING
     * @see CoreValues::PASSWORDS_DB_USER_NAME_SETTING
     * @see CoreValues::PASSWORDS_DB_PASSWORD_SETTING
     * @see CoreValues::PRIVILEGES_DB_NAME_SETTING
     * @see CoreValues::PRIVILEGES_DB_HOST_SETTING
     * @see CoreValues::PRIVILEGES_DB_USER_NAME_SETTING
     * @see CoreValues::PRIVILEGES_DB_PASSWORD_SETTING
     */
    public static function getSiteConfigValue(string $settingName): string
    {
        $config = self::getSiteConfigArray();
        return (isset($config[$settingName]) === true ? $config[$settingName] : '');
    }

    /**
     * Determines whether or not the site has been configured, i.e., whether or not this
     * is a fresh installation of the Darling Cms.
     *
     * @return bool True if site has been configured, false otherwise.
     */
    public static function siteConfigured(): bool
    {
        return (!empty(self::getSiteConfigArray()) === true && self::verifyCoreSettings() === true);
    }

    /**
     * This method will check that all required site configuration settings are defined.
     *
     * Note: The site configuration settings whose names correspond to the following
     *       constants MUST be defined in the site's configuration file:
     *
     * - CoreValues::CORE_DB_NAME_SETTING : Name of the site configuration setting that
     *                                      defines the name of the database used by Core.
     *
     * - CoreValues::CORE_DB_HOST_SETTING : Name of the site configuration setting that
     *                                      defines the name of host for the database
     *                                      used by Core.
     *
     * - CoreValues::CORE_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the Core database user's
     *                                           username.
     *
     * - CoreValues::CORE_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                          that defines the Core database user's
     *                                          password.
     *
     * - CoreValues::APPS_DB_NAME_SETTING : Name of the site configuration setting that
     *                                      defines the name of the database used by Apps.
     *
     * - CoreValues::APPS_DB_HOST_SETTING : Name of the site configuration setting that
     *                                      defines the name of host for the database
     *                                      used by Apps.
     *
     * - CoreValues::APPS_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the Apps database user's
     *                                           username.
     *
     * - CoreValues::APPS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                          that defines the Apps database user's
     *                                          password.
     *
     * - CoreValues::USERS_DB_NAME_SETTING : Name of the site configuration setting that
     *                                       defines the name of the database used to
     *                                       store User data.
     *
     * - CoreValues::USERS_DB_HOST_SETTING : Name of the site configuration setting that
     *                                       defines the name of host for the database
     *                                       used to store User data.
     *
     * - CoreValues::USERS_DB_USER_NAME_SETTING : Name of the site configuration setting
     *                                            that defines the Users database user's
     *                                            username.
     *
     * - CoreValues::USERS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                           that defines the Users database user's
     *                                           password.
     *
     * - CoreValues::PASSWORDS_DB_NAME_SETTING : Name of the site configuration setting
     *                                           that defines the name of the database
     *                                           used to store Password data.
     *
     * - CoreValues::PASSWORDS_DB_HOST_SETTING : Name of the site configuration setting
     *                                           that defines the name of host for the
     *                                           database used to store Password data.
     *
     * - CoreValues::PASSWORDS_DB_USER_NAME_SETTING : Name of the site configuration
     *                                                setting that defines the Passwords
     *                                                database user's username.
     *
     * - CoreValues::PASSWORDS_DB_PASSWORD_SETTING : Name of the site configuration setting
     *                                               that defines the Passwords database
     *                                               user's password.
     *
     * - CoreValues::PRIVILEGES_DB_NAME_SETTING : Name of the site configuration setting
     *                                            that defines the name of the database
     *                                            used to store Privileges, i.e., Actions,
     *                                            Permissions, Roles.
     *
     * - CoreValues::PRIVILEGES_DB_HOST_SETTING : Name of the site configuration setting
     *                                            that defines the name of host for the
     *                                            database used to store Privileges,
     *                                            i.e., Actions, Permissions, and Roles.
     *
     * - CoreValues::PRIVILEGES_DB_USER_NAME_SETTING : Name of the site configuration
     *                                                 setting that defines the Privileges
     *                                                 database user's username.
     *
     * - CoreValues::PRIVILEGES_DB_PASSWORD_SETTING : Name of the site configuration
     *                                                setting that defines the Privileges
     *                                                database user's password.
     *
     * @return bool True if all required site configuration settings are defined, false otherwise.
     *
     * @see CoreValues::CORE_DB_NAME_SETTING
     * @see CoreValues::CORE_DB_HOST_SETTING
     * @see CoreValues::CORE_DB_USER_NAME_SETTING
     * @see CoreValues::CORE_DB_PASSWORD_SETTING
     * @see CoreValues::APPS_DB_NAME_SETTING
     * @see CoreValues::APPS_DB_HOST_SETTING
     * @see CoreValues::APPS_DB_USER_NAME_SETTING
     * @see CoreValues::APPS_DB_PASSWORD_SETTING
     * @see CoreValues::USERS_DB_NAME_SETTING
     * @see CoreValues::USERS_DB_HOST_SETTING
     * @see CoreValues::USERS_DB_USER_NAME_SETTING
     * @see CoreValues::USERS_DB_PASSWORD_SETTING
     * @see CoreValues::PASSWORDS_DB_NAME_SETTING
     * @see CoreValues::PASSWORDS_DB_HOST_SETTING
     * @see CoreValues::PASSWORDS_DB_USER_NAME_SETTING
     * @see CoreValues::PASSWORDS_DB_PASSWORD_SETTING
     * @see CoreValues::PRIVILEGES_DB_NAME_SETTING
     * @see CoreValues::PRIVILEGES_DB_HOST_SETTING
     * @see CoreValues::PRIVILEGES_DB_USER_NAME_SETTING
     * @see CoreValues::PRIVILEGES_DB_PASSWORD_SETTING
     */
    private static function verifyCoreSettings()
    {
        $status = array();
        $config = self::getSiteConfigArray();
        foreach (self::getRequiredSettingNames() as $requiredSetting) {
            array_push($status, in_array($requiredSetting, array_keys($config), true));
        }
        return !in_array(false, $status, true);
    }

    /**
     * Returns an array of the names of the site configuration settings
     * that MUST be defined in a site's configuration file.
     *
     * @return array Array of the names of the site configuration settings
     *               that MUST be defined a site's configuration file.
     *
     * @devNote: Whenever a new site configuration setting is required, make
     *           sure to add it to the array returned by this method.
     *
     * @devNote: Whenever a site configuration is no longer required make sure to
     *           remove it from the array returned by this method.
     */
    public static function getRequiredSettingNames(): array
    {
        return array(
            /* Core DB Required Configuration Setting Names */
            self::CORE_DB_NAME_SETTING,
            self::CORE_DB_HOST_SETTING,
            self::CORE_DB_USER_NAME_SETTING,
            self::CORE_DB_PASSWORD_SETTING,
            /* Apps DB Required Configuration Setting Names */
            self::APPS_DB_NAME_SETTING,
            self::APPS_DB_HOST_SETTING,
            self::APPS_DB_USER_NAME_SETTING,
            self::APPS_DB_PASSWORD_SETTING,
            /* Users DB Required Configuration Setting Names */
            self::USERS_DB_NAME_SETTING,
            self::USERS_DB_HOST_SETTING,
            self::USERS_DB_USER_NAME_SETTING,
            self::USERS_DB_PASSWORD_SETTING,
            /* Passwords DB Required Configuration Setting Names */
            self::PASSWORDS_DB_NAME_SETTING,
            self::PASSWORDS_DB_HOST_SETTING,
            self::PASSWORDS_DB_USER_NAME_SETTING,
            self::PASSWORDS_DB_PASSWORD_SETTING,
            /* Privileges DB Required Configuration Setting Names */
            self::PRIVILEGES_DB_NAME_SETTING,
            self::PRIVILEGES_DB_HOST_SETTING,
            self::PRIVILEGES_DB_USER_NAME_SETTING,
            self::PRIVILEGES_DB_PASSWORD_SETTING,
        );
    }

    /**
     * Returns the site's directory's name.
     *
     * @return string The site's directory's name.
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
     * Note: To get the site's complete root url use the CoreValues::getSiteRootUrl()
     *       method.
     *
     * WARNING: This method utilizes PHP's parse_url() function to derive the site's
     *          name from the site root url, if parse_url() fails then the value of
     *          the CoreValues::DEFAULT_SITE_URL_NAME constant will be returned.
     *
     * Note: This method will try to accommodate a local environment.
     *
     * For instance:
     *
     * On a local environment the root url may look something like
     * "http://localhost/example.dev" or "http://localhost:8888/example.dev"
     * in which case this method will attempt to derive the site url name
     * from the path, which would return "example.dev" if successful. If the
     * path cannot be used for some reason, then either "localhost" or the
     * default site url name defined by the CoreValues::::DEFAULT_SITE_URL_NAME
     * will be returned. This only applies to a local environment.
     *
     * @return string The site name derived from the site's root url.
     *
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
         * For localhost use 'path', this is necessary b/c on a local environment the
         * site will typically follow a format such as "http://localhost/example.dev/foo"
         * or "http://localhost:8888/example.dev/foo" and for such a url parse_url will
         * return "localhost" as the 'host', not "example.dev".
         *
         * Note: If parse_url() failed to determine path, i.e. path is not set or is empty,
         * then proceed using host, which for local environment will most likely be
         * "localhost".
         */
        if ($parsedUrl['host'] === 'localhost' && empty($parsedUrl['path']) === false && $parsedUrl['path'] !== '/') {
            /**
             * Filter the path so only the chars following the first / are used and chars
             * following any subsequent / are excluded.
             * i.e. if the path is /example.dev/foo/bar/baz exclude /foo/bar/baz
             */
            $localPath = explode('/', $parsedUrl['path'])[1];
            return str_replace($replace, '', $localPath);
        }
        return str_replace($replace, '', $parsedUrl['host']);
    }

    /**
     * Returns the name of the database used by Core.
     *
     * @return string The name of the database used by Core.
     */
    public static function getCoreDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::CORE_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::CORE_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used by Apps.
     *
     * @return string The name of the database used by Apps.
     */
    public static function getAppsDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::APPS_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::APPS_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used to store User data.
     *
     * @return string The name of the database used to store User data.
     */
    public static function getUsersDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::USERS_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::USERS_DB_NAME_SETTING) : '');
    }


    /**
     * Returns the name of the database used to store Passwords.
     *
     * @return string The name of the database used to store Passwords.
     */
    public static function getPasswordsDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::PASSWORDS_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PASSWORDS_DB_NAME_SETTING) : '');
    }

    /**
     * Returns the name of the database used to store Privileges, i.e., the name
     * of the database used to store Actions, Permissions, and Roles.
     *
     * @return string The name of the database used to store Privileges.
     */
    public static function getPrivilegesDBName(): string
    {
        return (empty(self::getSiteConfigValue(self::PRIVILEGES_DB_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PRIVILEGES_DB_NAME_SETTING) : '');

    }

    /**
     * Returns the name of the database host for the specified database as defined
     * in the site's configuration file.
     *
     * @param string $databaseName The name of the database. It is recommended
     *                             that one of the CoreValues::get*DBName()
     *                             methods is used to pass this parameter a
     *                             value. See note^ below for more information.
     *
     * Note^: This method can only retrieve a host name for databases whose names
     *        can be retrieved by one of the following methods:
     *
     *       CoreValues::getCoreDBName()
     *
     *       CoreValues::getAppsDBName()
     *
     *       CoreValues::getUsersDBName()
     *
     *       CoreValues::getPasswordsDBName()
     *
     *       CoreValues::getPrivilegesDBName()
     *
     * For all other databases, use the CoreValues::getSiteConfigValue() method directly.
     *
     * @return string The name of the host for the specified database.
     *
     * @return string The name of the database host.
     * @see CoreValues::getAppsDBName()
     * @see CoreValues::getUsersDBName()
     * @see CoreValues::getPasswordsDBName()
     * @see CoreValues::getPrivilegesDBName()
     * @see CoreValues::getSiteConfigValue()
     * @see CoreValues::getCoreDBName()
     */
    public static function getDBHostName(string $databaseName): string
    {
        switch ($databaseName) {
            case CoreValues::getCoreDBName():
                return (empty(self::getSiteConfigValue(self::CORE_DB_HOST_SETTING)) === false ? self::getSiteConfigValue(self::CORE_DB_HOST_SETTING) : '');
            case CoreValues::getAppsDBName():
                return (empty(self::getSiteConfigValue(self::APPS_DB_HOST_SETTING)) === false ? self::getSiteConfigValue(self::APPS_DB_HOST_SETTING) : '');
            case CoreValues::getUsersDBName():
                return (empty(self::getSiteConfigValue(self::USERS_DB_HOST_SETTING)) === false ? self::getSiteConfigValue(self::USERS_DB_HOST_SETTING) : '');
            case CoreValues::getPasswordsDBName():
                return (empty(self::getSiteConfigValue(self::PASSWORDS_DB_HOST_SETTING)) === false ? self::getSiteConfigValue(self::PASSWORDS_DB_HOST_SETTING) : '');
            case CoreValues::getPrivilegesDBName():
                return (empty(self::getSiteConfigValue(self::PRIVILEGES_DB_HOST_SETTING)) === false ? self::getSiteConfigValue(self::PRIVILEGES_DB_HOST_SETTING) : '');
        }
        return '';
    }

    /**
     * Returns the username for the specified database as defined in the site's
     * configuration file.
     *
     * @param string $databaseName The name of the database. It is recommended that
     *                             one of the CoreValues::get*DBName() methods is used
     *                             to pass this parameter a value. See note^ below for
     *                             more information.
     *
     * Note^: This method can only retrieve a username for databases whose name can be
     *        retrieved by one of the following methods:
     *
     *       CoreValues::getCoreDBName()
     *
     *       CoreValues::getAppsDBName()
     *
     *       CoreValues::getUsersDBName()
     *
     *       CoreValues::getPasswordsDBName()
     *
     *       CoreValues::getPrivilegesDBName()
     *
     * For all other databases, use the CoreValues::getSiteConfigValue() method directly.
     *
     * @return string The username for the specified database.
     *
     * @see CoreValues::getCoreDBName()
     * @see CoreValues::getAppsDBName()
     * @see CoreValues::getUsersDBName()
     * @see CoreValues::getPasswordsDBName()
     * @see CoreValues::getPrivilegesDBName()
     * @see CoreValues::getSiteConfigValue()
     */
    public static function getDBUserName(string $databaseName): string
    {
        switch ($databaseName) {
            case CoreValues::getCoreDBName():
                return (empty(self::getSiteConfigValue(self::CORE_DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::CORE_DB_USER_NAME_SETTING) : '');
                break;
            case CoreValues::getAppsDBName():
                return (empty(self::getSiteConfigValue(self::APPS_DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::APPS_DB_USER_NAME_SETTING) : '');
                break;
            case CoreValues::getUsersDBName():
                return (empty(self::getSiteConfigValue(self::USERS_DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::USERS_DB_USER_NAME_SETTING) : '');
                break;
            case CoreValues::getPasswordsDBName():
                return (empty(self::getSiteConfigValue(self::PASSWORDS_DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PASSWORDS_DB_USER_NAME_SETTING) : '');
                break;
            case CoreValues::getPrivilegesDBName():
                return (empty(self::getSiteConfigValue(self::PRIVILEGES_DB_USER_NAME_SETTING)) === false ? self::getSiteConfigValue(self::PRIVILEGES_DB_USER_NAME_SETTING) : '');
                break;
            default:
                return '';
        }
    }

    /**
     * Returns the user password for the specified database as defined in the site's
     * configuration file.
     *
     * @param string $databaseName The name of the database. It is recommended that
     *                             one of the CoreValues::get*DBName() methods is
     *                             used to pass this parameter a value. See note^
     *                             below for more information.
     *
     * Note^: This method can only retrieve a user password for databases whose names
     *        can be retrieved by one of the following methods:
     *
     *       CoreValues::getCoreDBName()
     *
     *       CoreValues::getAppsDBName()
     *
     *       CoreValues::getUsersDBName()
     *
     *       CoreValues::getPasswordsDBName()
     *
     *       CoreValues::getPrivilegesDBName()
     *
     * For all other databases, use the CoreValues::getSiteConfigValue() method directly.
     *
     * @return string The user password for the specified database.
     *
     * @see CoreValues::getCoreDBName()
     * @see CoreValues::getAppsDBName()
     * @see CoreValues::getUsersDBName()
     * @see CoreValues::getPasswordsDBName()
     * @see CoreValues::getPrivilegesDBName()
     * @see CoreValues::getSiteConfigValue()
     */
    public static function getDBPassword(string $databaseName): string
    {
        switch ($databaseName) {
            case CoreValues::getCoreDBName():
                return (empty(self::getSiteConfigValue(self::CORE_DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::CORE_DB_PASSWORD_SETTING) : '');
                break;
            case CoreValues::getAppsDBName():
                return (empty(self::getSiteConfigValue(self::APPS_DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::APPS_DB_PASSWORD_SETTING) : '');
                break;
            case CoreValues::getUsersDBName():
                return (empty(self::getSiteConfigValue(self::USERS_DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::USERS_DB_PASSWORD_SETTING) : '');
                break;
            case CoreValues::getPasswordsDBName():
                return (empty(self::getSiteConfigValue(self::PASSWORDS_DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::PASSWORDS_DB_PASSWORD_SETTING) : '');
                break;
            case CoreValues::getPrivilegesDBName():
                return (empty(self::getSiteConfigValue(self::PRIVILEGES_DB_PASSWORD_SETTING)) === false ? self::getSiteConfigValue(self::PRIVILEGES_DB_PASSWORD_SETTING) : '');
                break;
            default:
                return '';
        }
    }

    /**
     * Returns the site's root url.
     *
     * @return string The site's root url.
     */
    public static function getSiteRootUrl(): string
    {
        return CoreValues::determineRootUrl((!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /**
     * Discerns the root url of a specified url.
     *
     * Example:
     *
     *      // The following would return: http://www.example.org/
     *
     *      CoreValues::determineRootUrl('http://www.example.org/some/sub/path/someFile.txt');
     *
     * @param string $url The url whose root url should be discerned.
     *
     * @return string The root url discerned from the specified url.
     */
    private static function determineRootUrl(string $url): string
    {
        $rootUrlPieces = CoreValues::parseUrl($url);
        if (empty(array_filter($rootUrlPieces, 'strlen')) === true) {
            return CoreValues::DEFAULT_SITE_ROOT_URL;
        }
        // Determine sub scheme url, i.e., part of url that comes after http://
        $subSchemeUrl = $rootUrlPieces['host'] . (!empty($rootUrlPieces['port']) ? ':' . $rootUrlPieces['port'] : '') . $rootUrlPieces['path'];
        // Determine root of url from the pieces of the $subSchemeUrl
        $subSchemeUrlPieces = explode('/', $subSchemeUrl);
        switch ($rootUrlPieces['host']) {
            // localhost will always be first and possibly second sub scheme url pieces
            case 'localhost':
                $root = $subSchemeUrlPieces[0] . '/' . (!empty($subSchemeUrlPieces[1]) ? $subSchemeUrlPieces[1] . '/' : '');
                break;
            // default will always be first sub scheme url piece
            default:
                $root = $subSchemeUrlPieces[0] . '/';
                break;
        }
        // Construct and return the Site's root url
        return CoreValues::urlEncode((!empty($rootUrlPieces['scheme']) ? $rootUrlPieces['scheme'] : 'http') . '://' . $root);
    }

    /**
     * Similar to PHP's parse_url() function, this method parses a URL and
     * returns an associative array containing the various components of
     * the URL.
     *
     * Unlike PHP's parse_url() will always return an array
     * with values set for the following indexes:
     *
     * - scheme (e.g., http)
     *
     * - host
     *
     * - port
     *
     * - user
     *
     * - pass
     *
     * - path
     *
     * - query (after the question mark ?)
     *
     * - fragment (after the hashmark #)
     *
     * Note: An empty string will be set for any of the above url components
     * that are not discernible, or do not exist.
     *
     * Note: The values of the array elements are not URL decoded.
     *
     * Note: This function is not meant to validate the given URL, it only
     * breaks it up into individual components.
     *
     * @param string $url The url to parse.
     *
     * @return array An associative array of the url's discernible components.
     *               Note: Empty strings will be assigned to any components
     *               that could not be discerned, or do not exist.
     *
     * @devNote: This method is declared private because it does not return a "Core Value",
     *           it is designed to be used internally by the CoreValues class's methods.
     *
     * @see parse_url()
     */
    final private static function parseUrl(string $url): array
    {
        $parsedUrl = parse_url($url);
        if (is_array($parsedUrl) === false) {
            return array('scheme' => '', 'host' => '', 'port' => '', 'user' => '', 'pass' => '', 'path' => '', 'query' => '', 'fragment' => '');
        }
        return array(
            'scheme' => (!empty($parsedUrl['scheme']) && is_string($parsedUrl['scheme']) ? $parsedUrl['scheme'] : ''),
            'host' => (!empty($parsedUrl['host']) && is_string($parsedUrl['host']) ? $parsedUrl['host'] : ''),
            'port' => (!empty($parsedUrl['port']) && is_int($parsedUrl['port']) ? strval($parsedUrl['port']) : ''),
            'user' => (!empty($parsedUrl['user']) && is_string($parsedUrl['user']) ? $parsedUrl['user'] : ''),
            'pass' => (!empty($parsedUrl['pass']) && is_string($parsedUrl['pass']) ? $parsedUrl['pass'] : ''),
            'path' => (!empty($parsedUrl['path']) && is_string($parsedUrl['path']) ? $parsedUrl['path'] : ''),
            'query' => (!empty($parsedUrl['query']) && is_string($parsedUrl['query']) ? $parsedUrl['query'] : ''),
            'fragment' => (!empty($parsedUrl['fragment']) && is_string($parsedUrl['fragment']) ? $parsedUrl['fragment'] : '')
        );
    }

    /**
     * Similar to PHP's urlencode(), this method returns a string in which all
     * non-alphanumeric characters in the specified $url, except "-","_", and
     * "." have been replaced with a percent (%) sign followed by two hex digits.
     * This method also makes an exception for the following characters:
     *
     * - / (forward slashes will not be encoded by this method)
     *
     * - : (colons will not be encoded by this method)
     *
     * Note: An important difference in how this method works as opposed to PHP's
     * urlencode(), is spaces are always encoded as %20, if this is not desired
     * it is best to use PHP's urlencode().
     *
     * WARNING: If a more standard url-encoding is needed it is best to use PHP's
     * urlencode() function as this method is tailored to the needs of the CoreValues
     * class.
     *
     * @param string $url The url to encode.
     *
     *
     * @return string The encoded url.
     *
     * @see urlencode()
     */
    final private static function urlEncode(string $url): string
    {
        $standerEncoding = urlencode($url);
        return str_replace(['%3A', '%2F', '+'], [':', '/', '%20'], $standerEncoding);

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
     * Returns the path to the directory where the site's javascript libraries are stored.
     * @return string The path to the directory where the site's javascript libraries are
     *                stored.
     */
    public static function getJsLibRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/js';
    }

    /**
     * Returns the path to the specified javascript library's directory.
     *
     * @param string $jsLibraryName The name of the javascript library whose path
     *                              should be returned.
     *
     * @return string The path to the specified Js Library's directory.
     */
    public static function getJsLibDirPath(string $jsLibraryName): string
    {
        return self::getJsLibRootDirPath() . '/' . $jsLibraryName;
    }

    /**
     * Returns the path to the site's themes directory.
     *
     * @return string The path to the site's themes directory.
     */
    public static function getThemesRootDirPath(): string
    {
        return self::getSiteRootDirPath() . '/themes';
    }

    /**
     * Returns the path to the specified theme's directory.
     *
     * @param string $themeName The name of the theme whose path should be returned.
     *
     * @return string The path to the specified theme's directory.
     */
    public static function getThemeDirPath(string $themeName): string
    {
        return self::getThemesRootDirPath() . '/' . $themeName;
    }

}
