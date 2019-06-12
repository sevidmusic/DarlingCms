<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 10/10/18
 * Time: 2:24 PM
 */

namespace DarlingCms\classes\info;

use DarlingCms\classes\startup\AppStartup;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\accessControl\IAppConfig;
use DarlingCms\interfaces\startup\IAppStartup;
use DirectoryIterator;

/**
 * Class AppInfo. Provides information about installed Darling Cms apps,
 * including information necessary for startup and configuration.
 *
 * @package DarlingCms\classes\info
 *
 * @see AppInfo::setAppConfigPaths()
 * @see AppInfo::getAppConfigPaths()
 * @see AppInfo::setAppNamespaces()
 * @see AppInfo::getAppNamespaces()
 * @see AppInfo::setAppConfigObjects()
 * @see AppInfo::getAppConfigObjects()
 * @see AppInfo::setAppStartupObjects()
 * @see AppInfo::getAppStartupObjects()
 * @see AppInfo::getAppDirPath()
 * @see AppInfo::addAppName()
 * @see AppInfo::getAppNames()
 */
class AppInfo
{
    /**
     * @var int This constant can be passed to the __construct() method's
     *          $filterMode parameter to indicate this AppInfo instance
     *          should provide information about all installed Darling
     *          Cms apps. This is the default.
     */
    public const NO_FILTER = 0;

    /**
     * @var int This constant can be passed to the __construct() method's
     *          $filterMode parameter to indicate this AppInfo instance
     *          should only provide information about the Darling Cms apps
     *          specified in the $appNames property's array.
     *          i.e., Only provide information on whitelisted apps.
     */
    public const WHITELIST = 2;

    /**
     * @var int This constant can be passed to the __construct() method's
     *          $filterMode parameter to indicate this AppInfo instance
     *          should only provide information about the Darling Cms apps
     *          not specified in the $appNames property's array.
     *          i.e., Exclude information about blacklisted apps.
     */
    public const BLACKLIST = 4;

    /**
     * @var int Determines which app's information will be provided by
     *          this AppInfo instance.
     *
     *          Note: It is recommended that one of the following class constants
     *          be used to set this property's value:
     *
     *          - AppInfo::NO_FILTER
     *
     *          - AppInfo::WHITELIST
     *
     *          - AppInfo::BLACKLIST
     *
     * @see AppInfo::NO_FILTER
     * @see AppInfo::WHITELIST
     * @see AppInfo::BLACKLIST
     *
     */
    private $filterMode = 0;

    /**
     * @var array Array of the names of the apps whose information will be included or excluded
     *            depending on the value of the $filterMode property.
     *
     * @see AppInfo::$filterMode
     */
    private $appNames = array();

    /**
     * @var array Array of paths to each app's respective AppConfig.php file.
     */
    private $appConfigPaths = array();

    /**
     * @var array Array of namespaces for each app.
     */
    private $appNamespaces = array();

    /**
     * @var array Array of instances of each app's respective IAppConfig implementation.
     *
     * @see IAppConfig
     */
    private $appConfigObjects = array();

    /**
     * @var array Array of instances of each app's respective IAppStartup implementation.
     *
     * @see IAppStartup
     */
    private $appStartupObjects = array();


    /**
     * AppInfo constructor. Determines the paths to each app's AppConfig.php file,
     * determines each app's namespace, instantiates an appropriate IAppConfig
     * implementation instance for each app, instantiates an appropriate IAppStartup
     * implementation instance for each app, and determines which app's information
     * should be provided by this AppInfo instance.
     *
     * @param int $filterMode Determines which app's information will be provided
     *                        by this AppInfo instance.
     *
     *                        Note: It is recommended that one of the following class
     *                        constants be used to set this parameter's value:
     *
     *                        - AppInfo::NO_FILTER (Default)
     *
     *                        - AppInfo::WHITELIST
     *
     *                        - AppInfo::BLACKLIST
     *
     * @param array $appNames Name of the apps whose information will be included
     *                        or excluded depending on the value of the $filterMode
     *                        property, i.e. the value passed to this method's
     *                        $filterMode parameter.
     *
     * @see AppInfo::$filterMode
     * @see AppInfo::NO_FILTER
     * @see AppInfo::WHITELIST
     * @see AppInfo::BLACKLIST
     * @see AppInfo::$appNames
     * @see AppInfo::setAppConfigPaths()
     * @see AppInfo::setAppNamespaces()
     * @see AppInfo::setAppConfigObjects()
     * @see AppInfo::setAppStartupObjects()
     */
    public function __construct($filterMode = 0, array $appNames = array())
    {
        $this->filterMode = $filterMode;
        foreach ($appNames as $app) {
            $this->addAppName($app);
        }
        $this->setAppConfigPaths();
        $this->setAppNamespaces();
        $this->setAppConfigObjects();
        $this->setAppStartupObjects();
    }

    /**
     * Determines the absolute path to each app's AppConfig.php file and assigns
     * it to the the $appConfigPaths property's array.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's
     * array for any apps that do not provide an AppConfig.php file. Furthermore,
     * this method will log an error for any apps that do not provide an
     * AppConfig.php file.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's
     * array for any apps that are assigned to the $excludedApps property's array,
     * i.e., apps that were passed to the excludeApp() method.
     *
     * @see $appNames
     * @see AppInfo::addAppName()
     * @see \DirectoryIterator
     * @see \DirectoryIterator::getRealPath()
     * @see \DirectoryIterator::getFilename()
     * @see \DirectoryIterator::isDir()
     * @see \DirectoryIterator::isDot()
     */
    private function setAppConfigPaths(): void
    {
        $appDirIterator = new DirectoryIterator($this->getAppDirPath());
        foreach ($appDirIterator as $appDirectoryIterator) {
            $appConfigPath = $appDirectoryIterator->getRealPath() . '/AppConfig.php';
            if ($this->validateApp($appDirectoryIterator->getFilename()) === true && $this->validateAppDirectory($appDirectoryIterator) === true) {
                switch (file_exists($appConfigPath)) {
                    case false:
                        error_log('Darling Cms Error: ' . $appDirectoryIterator->getFilename() . ' does not provide an AppConfig.php file.');
                        break;
                    default:
                        array_push($this->appConfigPaths, $appConfigPath);
                        break;
                }
            }
        }
    }


    /**
     * Determines whether or not it is valid to provide information about
     * the specified app. Below is an overview of what is considered valid
     * based on what the $filterMode property is set to:
     *
     * - If the $filterMode property is set to AppInfo::NO_FILTER, then
     *   all apps will be considered valid.
     * - If the $filterMode property is set to AppInfo::WHITELIST then
     *   only the apps that are assigned to the $appNames property's
     *   array will be considered valid.
     * - If the $filterMode property is set to AppInfo::BLACKLIST then
     *   only apps not assigned to the $appNames property's array will
     *   be considered valid.
     *
     * Note: This method will log an error and return false if the $filterMode
     * property's value is invalid.
     *
     * @param string $appName The name of the app to validate.
     *
     * @return bool True if it is valid to provide information about the
     *              specified app, false otherwise.
     */
    private function validateApp(string $appName): bool
    {
        switch ($this->filterMode) {
            case self::NO_FILTER:
                return true;
            case self::WHITELIST:
                return in_array($appName, $this->appNames, true) === true;
            case self::BLACKLIST:
                return in_array($appName, $this->appNames, true) === false;
        }
        error_log(sprintf("AppInfo Error: Invalid filter mode \"%s\". Filter mode must be one of the following: NO_FILTER (%s), WHITELIST (%s), or BLACKLIST (%s)", strval($this->filterMode), self::NO_FILTER, self::WHITELIST, self::BLACKLIST));
        return false;
    }

    /**
     * Determines whether or not a directory is a valid Darling Cms app directory.
     * @param DirectoryIterator $appDirectoryIterator A DirectoryIterator instance for the
     *                                                directory being validated.
     * @return bool True if directory is a valid Darling Cms app directory, false otherwise.
     */
    private function validateAppDirectory(DirectoryIterator $appDirectoryIterator): bool
    {
        return $appDirectoryIterator->isDir() === true && $appDirectoryIterator->isDot() === false;
    }

    /**
     * Returns an array of paths to the AppConfig.php files defined by each app.
     *
     * Note: The returned array will not include paths to AppConfig.php files
     * that belong to apps assigned to the $excludedApps property's array,
     * i.e., apps that were passed to the excludeApp() method.
     *
     * @return array An array of paths to the AppConfig.php files defined by each app.
     */
    public function getAppConfigPaths(): array
    {
        return $this->appConfigPaths;
    }

    /**
     * Determines each app's namespace and assigns it to the $appNamespaces
     * property's array.
     */
    private function setAppNamespaces(): void
    {
        foreach ($this->appConfigPaths as $appConfigPath) {
            array_push($this->appNamespaces, '\\' . ucfirst(str_replace('/', '\\', str_replace(array(str_replace('core/classes/info', '', __DIR__), 'AppConfig.php'), '', $appConfigPath))));
        }
    }

    /**
     * Returns an array of namespaces for each app.
     *
     * Note: The returned array will not include namespaces that belong to apps assigned
     * to the $excludedApps property's array, i.e., apps that were passed to the
     * excludeApp() method.
     *
     * @return array An array of namespaces for each app.
     *
     */
    public function getAppNamespaces(): array
    {
        return $this->appNamespaces;
    }

    /**
     * Instantiates an instance of each app's respective IAppConfig implementation.
     *
     * WARNING: This method will not perform instantiation, and will log an error
     * for any apps that do not define an AppConfig class, or any app's that define
     * an AppConfig class that does not implement
     * the DarlingCms\interfaces\accessControl\IAppConfig interface.
     *
     * @see IAppConfig
     */
    private function setAppConfigObjects(): void
    {
        foreach ($this->appNamespaces as $appNamespace) {
            $appConfig = $appNamespace . 'AppConfig';
            if (class_exists($appConfig) === true && in_array('DarlingCms\interfaces\accessControl\IAppConfig', class_implements($appConfig), true) === true) {
                array_push($this->appConfigObjects, new $appConfig);
                continue;
            }
            $appName = str_replace(array('\\', 'Apps'), '', $appNamespace);
            error_log('Darling Cms Error: The ' . $appName . ' app\'s AppConfig.php file does not define an implementation of the DarlingCms\interfaces\accessControl\IAppConfig interface.');
        }
    }

    /**
     * Returns an array of IAppConfig implementations defined by each app.
     *
     * Note: The returned array will not include IAppConfig implementations
     * defined by apps assigned to the $excludedApps property's array,
     * i.e., apps that were passed to the excludeApp() method.
     *
     * @return array An array of IAppConfig implementations defined by each app.
     *
     */
    public function getAppConfigObjects(): array
    {
        return $this->appConfigObjects;
    }

    /**
     * Instantiates an IAppStartup implementation instance for each app.
     *
     * Note: At the moment, the \DarlingCms\classes\startup\AppStartup
     * implementation of the IAppStartup interface is used for all apps.
     *
     * @see IAppStartup
     * @see AppStartup
     */
    private function setAppStartupObjects(): void
    {
        foreach ($this->appConfigObjects as $appConfigObject) {
            array_push($this->appStartupObjects, new AppStartup($appConfigObject));
        }
    }

    /**
     * Returns an array of IAppStartup implementation instances instantiated for each app.
     *
     * Note: The returned array will not include IAppStartup implementation instances
     * for apps assigned to the $excludedApps property's array, i.e. apps that were
     * passed to the excludeApp() method.
     *
     * @return array|IAppStartup An array of IAppStartup implementations instances
     *                           instantiated for each app.
     *
     */
    public function getAppStartupObjects(): array
    {
        return $this->appStartupObjects;
    }

    /**
     * Returns the path to the apps directory.
     *
     * @return string The path to the apps directory.
     *
     * @see CoreValues::getAppsRootDirPath()
     */
    public function getAppDirPath(): string
    {
        return CoreValues::getAppsRootDirPath();
    }

    /**
     * Adds the specified $appName to the $appNames property's array.
     *
     * @param string ...$appName The name of the app to add.
     */
    public function addAppName(string ...$appName): void
    {
        foreach ($appName as $app) {
            array_push($this->appNames, $app);
        }
    }

    /**
     * Returns an array of the apps that will be excluded from this AppInfo
     * instance, i.e., apps that were passed to the excludeApp() method.
     *
     * @return array An array of the apps that will be excluded from this
     *               AppInfo instance.
     */
    public function getAppNames(): array
    {
        return $this->appNames;
    }
}
