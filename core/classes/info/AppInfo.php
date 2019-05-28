<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 10/10/18
 * Time: 2:24 PM
 */

namespace DarlingCms\classes\info;

use DarlingCms\classes\startup\AppStartup;
use DarlingCms\classes\startup\AppStartupJsonCache;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\startup\IAppStartup;
use DirectoryIterator;

/**
 * Class AppInfo. Provides information about installed apps necessary for startup and configuration.
 * @package DarlingCms\classes\info
 * @see AppInfo::setAppConfigPaths()
 * @see AppInfo::getAppConfigPaths()
 * @see AppInfo::setAppNamespaces()
 * @see AppInfo::getAppNamespaces()
 * @see AppInfo::setAppConfigObjects()
 * @see AppInfo::getAppConfigObjects()
 * @see AppInfo::setAppStartupObjects()
 * @see AppInfo::getAppStartupObjects()
 * @see AppInfo::getAppDirPath()
 * @see AppInfo::excludeApp()
 * @see AppInfo::getExcludedApps()
 */
class AppInfo
{
    /**
     * @var string The path to the Darling Cms apps directory.
     */
    private $appDirPath = '';

    /**
     * @var array Array of apps that will be excluded. Note: Use the excludeApp() method to assign apps to
     *            the $excludedApps property's array.
     * @see AppInfo::excludeApp()
     */
    private $excludedApps = array();

    /**
     * @var array Array of paths to the AppConfig.php files for each app.
     */
    private $appConfigPaths = array();

    /**
     * @var array Array of namespaces for each app.
     */
    private $appNamespaces = array();

    /**
     * @var array Array of the IAppConfig implementations defined by each app.
     */
    private $appConfigObjects = array();

    /**
     * @var array Array of IAppStartup implementations for each app. Note: The $startupMode property determines
     *            which IAppStartup implementation will be instantiated for each app.
     * @see $startupMode
     */
    private $appStartupObjects = array();

    /**
     * AppInfo constructor. Sets the startup mode, determines the path to the apps directory, determines
     * the paths to each app's AppConfig.php file, determines each app's namespace, and instantiates the
     * appropriate IAppStartup implementation for each app based on the $startupMode.
     * @param string ...$excludeApp Name of the app(s) that should be excluded from this AppInfo instance.
     *                              Note: To set more then one app to be excluded from this App Info instance,
     *                              pass additional app names as additional parameters.
     * @see $startupMode
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     * @see AppInfo::setAppConfigPaths()
     * @see AppInfo::setAppNamespaces()
     * @see AppInfo::setAppConfigObjects()
     * @see AppInfo::setAppStartupObjects()
     */
    public function __construct(string ...$excludeApp)
    {
        foreach ($excludeApp as $app) {
            $this->excludeApp($app);
        }
        $this->appDirPath = CoreValues::getAppsRootDirPath();
        $this->setAppConfigPaths();
        $this->setAppNamespaces();
        $this->setAppConfigObjects();
        $this->setAppStartupObjects();
    }

    /**
     * Determines the path to each app's AppConfig.php file, and assigns it to the the $appConfigPaths property's
     * array.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's array for any apps that do not
     * provide an AppConfig.php file. Furthermore, this method will log an error for any apps that do not provide
     * an AppConfig.php file.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's array for any apps that are
     * assigned to the $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     * @see \DirectoryIterator
     * @see \DirectoryIterator::getRealPath()
     * @see \DirectoryIterator::isDir()
     * @see \DirectoryIterator::isDot()
     */
    private function setAppConfigPaths(): void
    {
        $appDirIterator = new DirectoryIterator($this->appDirPath);
        foreach ($appDirIterator as $directoryIterator) {
            $appConfigPath = $directoryIterator->getRealPath() . '/AppConfig.php';
            if (in_array($directoryIterator->getFilename(), $this->excludedApps, true) === false && $directoryIterator->isDir() === true && $directoryIterator->isDot() === false) {
                switch (file_exists($appConfigPath)) {
                    case false:
                        error_log('Darling Cms Error: ' . $directoryIterator->getFilename() . ' does not provide an AppConfig.php file.');
                        break;
                    default:
                        array_push($this->appConfigPaths, $appConfigPath);
                        break;
                }
            }
        }
    }

    /**
     * Returns an array of paths to the AppConfig.php files defined by each app.
     * Note: The returned array will not include paths to AppConfig.php files that belong to apps assigned
     * to the $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of paths to the AppConfig.php files defined by each app.
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     */
    public function getAppConfigPaths(): array
    {
        return $this->appConfigPaths;
    }

    /**
     * Determines each app's namespace and assigns it to the $appNamespaces property's array.
     */
    private function setAppNamespaces(): void
    {
        foreach ($this->appConfigPaths as $appConfigPath) {
            array_push($this->appNamespaces, '\\' . ucfirst(str_replace('/', '\\', str_replace(array(str_replace('core/classes/info', '', __DIR__), 'AppConfig.php'), '', $appConfigPath))));
        }
    }

    /**
     * Returns an array of namespaces for each app.
     * Note: The returned array will not include namespaces that belong to apps assigned to the
     * $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of namespaces for each app.
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     */
    public function getAppNamespaces(): array
    {
        return $this->appNamespaces;
    }

    /**
     * Instantiates each app's DarlingCms\interfaces\accessControl\IAppConfig implementation.
     *
     * WARNING: This method will not perform instantiation, and will log an error for any apps that do not define
     * an AppConfig class, or any app's that define an AppConfig class that does not implement the
     * DarlingCms\interfaces\accessControl\IAppConfig interface.
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
     * Note: The returned array will not include IAppConfig implementations defined by apps assigned
     * to the $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of IAppConfig implementations defined by each app.
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     */
    public function getAppConfigObjects(): array
    {
        return $this->appConfigObjects;
    }

    /**
     * Instantiates an IAppStartup implementation for each app.
     * @see IAppStartup
     * @see AppStartup
     * @see AppStartupJsonCache
     */
    private function setAppStartupObjects(): void
    {
        foreach ($this->appConfigObjects as $appConfigObject) {
            array_push($this->appStartupObjects, new AppStartup($appConfigObject));
        }
    }

    /**
     * Returns an array of IAppStartup implementations instantiated for each app.
     * Note: The returned array will not include IAppStartup implementation instances for apps assigned
     * to the $excludedApps property's array, i.e. apps that were passed to the excludeApp() method.
     * @return array|IAppStartup An array of IAppStartup implementations instantiated for each app.
     * @see $excludedApps
     * @see AppInfo::excludeApp()
     */
    public function getAppStartupObjects(): array
    {
        return $this->appStartupObjects;
    }

    /**
     * Returns the path to the apps directory.
     * @return string The path to the apps directory.
     */
    public function getAppDirPath(): string
    {
        return $this->appDirPath;
    }

    /**
     * Exclude the specified app(s) from this App Info instance.
     * @param string ...$appName The name of the app to exclude from this App Info instance. To set more then one
     *                           app to be excluded from this App Info instance, pass additional app names as
     *                           additional parameters.
     */
    public function excludeApp(string ...$appName): void
    {
        foreach ($appName as $app) {
            array_push($this->excludedApps, $app);
        }
    }

    /**
     * Returns an array of the apps that will be excluded from this App Info instance, i.e., apps that were passed to
     * the excludeApp() method.
     * @return array An array of the apps that will be excluded from this App Info instance.
     */
    public function getExcludedApps(): array
    {
        return $this->excludedApps;
    }
}
