<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/29/18
 * Time: 11:03 PM
 */

namespace DarlingCms\classes\startup;


use DarlingCms\interfaces\accessControl\IAppConfig;
use DarlingCms\interfaces\startup\IAppStartup;

/**
 * Class MultiAppStartup. Defines an implementation of the IAppStartup interface that starts up multiple
 * Darling Cms apps.
 * @package DarlingCms\classes\startup
 * @see MultiAppStartup::getCssPaths()
 * @see MultiAppStartup::getJsPaths()
 * @see MultiAppStartup::getAppOutput()
 * @see MultiAppStartup::getPaths()
 * @see MultiAppStartup::startup()
 * @see MultiAppStartup::setAppConfigObjects()
 * @see MultiAppStartup::setAppStartupObjects()
 * @see MultiAppStartup::setAppConfigPaths()
 * @see MultiAppStartup::setAppNamespaces()
 * @see MultiAppStartup::shutdown()
 * @see MultiAppStartup::restart()
 * @see MultiAppStartup::getAppDirPath()
 * @see MultiAppStartup::getAppConfigPaths()
 * @see MultiAppStartup::getAppNamespaces()
 * @see MultiAppStartup::getAppConfigObjects()
 * @see MultiAppStartup::getAppStartupObjects()
 * @see MultiAppStartup::getExcludedApps()
 * @see MultiAppStartup::excludeApp()
 */
class MultiAppStartup implements IAppStartup
{
    /**
     * @var string Index assigned to the value of the path to the apps directory in the $paths property's array.
     */
    const APP_DIR_PATH_KEY = 'appDirPath';
    /**
     * @var string Index assigned to the array of paths to the respective AppConfig.php files in the $paths
     *             property's array.
     */
    const APP_CONFIG_PATHS_KEY = 'appConfigPaths';
    /**
     * @var string Index assigned to the array of css paths in the $paths property's array.
     */
    const CSS_PATHS_KEY = 'cssPaths';
    /**
     * @var string Index assigned to the array of js paths in the $paths property's array.
     */
    const JS_PATHS_KEY = 'jsPaths';

    /**
     * @var string Default startup mode, utilizes the AppStartup implementation of the IAppStartup interface.
     */
    const STARTUP_DEFAULT = 2;

    /**
     * @var string Startup mode that utilizes the AppStartupJsonCache implementation of the IAppStartup interface.
     */
    const STARTUP_JSON_CACHE = 4;

    /**
     * @var string The path to the Darling Cms apps directory.
     */
    private $appDirPath = '';

    /**
     * @var array Array of paths to the AppConfig.php files for each app in the apps directory.
     */
    private $appConfigPaths = array();

    /**
     * @var array Array of namespaces for each app in the apps directory.
     */
    private $appNamespaces = array();

    /**
     * @var array Array of the IAppConfig implementations defined by each app in the apps directory.
     */
    private $appConfigObjects = array();

    /**
     * @var array Array of IAppStartup implementations for each app in the apps directory.
     */
    private $appStartupObjects = array();

    /**
     * @var array Array of apps that will be excluded from startup.
     */
    private $excludedApps = array();

    /**
     * @var int The mode to use for startup. This determines which IAppStartup implementation is used.
     *          Supported modes are represented by the following class constants:
     *          - self::STARTUP_DEFAULT    Startup using the AppStartup implementation of the IAppStartup interface
     *                                     which does not use a caching mechanism.
     *          - self::STARTUP_JSON_CACHE Startup using the AppStartupJsonCache implementation of the IAppStartup
     *                                     interface which uses JSON as a caching mechanism.
     */
    private $startupMode = self::STARTUP_DEFAULT;

    /**
     * MultiAppStartup constructor. Determines the path to the Darling Cms apps directory and assigns it to the
     * $appDirPath property.
     * @param int $startupMode The mode to use for startup. This determines which IAppStartup implementation is used.
     *          Supported modes are represented by the following class constants:
     *          - self::STARTUP_DEFAULT    Startup using the AppStartup implementation of the IAppStartup interface
     *                                     which does not use a caching mechanism.
     *          - self::STARTUP_JSON_CACHE Startup using the AppStartupJsonCache implementation of the IAppStartup
     *                                     interface which uses JSON as a caching mechanism.
     */
    public function __construct(int $startupMode = 2)
    {
        $this->startupMode = $startupMode;
        $this->appDirPath = str_replace('core/classes/startup', 'apps', __DIR__);
        $this->setAppConfigPaths();
        $this->setAppNamespaces();
        $this->setAppConfigObjects();
        $this->setAppStartupObjects();
    }

    /**
     * Returns an array of paths to the css files belonging to the themes assigned to each app that started up
     * successfully.
     * @return array Array of paths to the css files belonging to the themes assigned to each app that started
     * up successfully.
     * @see IAppStartup::getCssPaths()
     */
    public function getCssPaths(): array
    {
        $cssPaths = array();
        foreach ($this->appStartupObjects as $startupObject) {
            foreach ($startupObject->getCssPaths() as $cssPath) {
                array_push($cssPaths, $cssPath);
            }
        }
        return array_unique($cssPaths);
    }

    /**
     * Returns an array of paths to the javascript files belonging to the javascript libraries assigned to each app
     * that started up successfully.
     * @return array Array of paths to the javascript files belonging to the javascript libraries assigned to each
     * app that started up successfully.
     * @see IAppStartup::getJsPaths()
     */
    public function getJsPaths(): array
    {
        $jsPaths = array();
        foreach ($this->appStartupObjects as $startupObject) {
            foreach ($startupObject->getJsPaths() as $jsPath) {
                array_push($jsPaths, $jsPath);
            }
        }
        return array_unique($jsPaths);
    }

    /**
     * Returns the collective output of the apps that started up successfully.
     * @return string The collective output of the apps that started up successfully.
     * @see IAppStartup::getAppOutput()
     */
    public function getAppOutput(): string
    {
        $output = '';
        foreach ($this->appStartupObjects as $startupObject) {
            $output .= $startupObject->getAppOutput();
        }
        return $output;
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
     * Returns an array of paths to the AppConfig.php files defined by each app in the apps directory.
     * Note: The returned array will not include paths to AppConfig.php files that belong to apps assigned
     * to the $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of paths to the AppConfig.php files defined by each app in the apps directory.
     */
    public function getAppConfigPaths(): array
    {
        return $this->appConfigPaths;
    }

    /**
     * Returns an array of namespaces for each app in the apps directory.
     * Note: The returned array will not include namespaces that belong to apps assigned to the
     * $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of namespaces for each app in the apps directory.
     */
    public function getAppNamespaces(): array
    {
        return $this->appNamespaces;
    }

    /**
     * Returns an array of IAppConfig implementations defined by each app in the apps directory.
     * Note: The returned array will not include IAppConfig implementations defined by apps assigned
     * to the $excludedApps property's array, i.e., apps that were passed to the excludeApp() method.
     * @return array An array of IAppConfig implementations defined by each app in the apps directory.
     */
    public function getAppConfigObjects(): array
    {
        return $this->appConfigObjects;
    }

    /**
     * Returns an array of IAppStartup implementations instantiated for each app in the apps directory.
     * Note: The returned array will not include IAppStartup implementation instances for apps assigned
     * to the $excludedApps property's array, i.e. apps that were passed to the excludeApp() method.
     * @return array An array of IAppStartup implementations instantiated for each app in the apps directory.
     */
    public function getAppStartupObjects(): array
    {
        return $this->appStartupObjects;
    }

    /**
     * Returns an array of the apps that will be excluded from startup, i.e., apps that were passed to
     * the excludeApp() method.
     * @return array An array of the apps that will be excluded from startup.
     */
    public function getExcludedApps(): array
    {
        return $this->excludedApps;
    }

    /**
     * Returns an array of the following paths: The path to the Darling Cms apps directory, an array of paths to
     * the AppConfig.php files defined by each of the apps in the apps directory, an array of paths to the css files
     * belonging to the themes assigned to each app in the apps directory, an array of paths to the javascript
     * files belonging to the javascript libraries assigned to each app in the apps directory.
     *
     * The paths are indexed using the class constants defined for each index:
     *
     * MultiAppStartup::APP_DIR_PATH_KEY : The path to the apps directory.
     *
     * MultiAppStartup::APP_CONFIG_PATHS_KEY : An array of paths to the AppConfig.php files defined by each app
     *                                         in the apps directory.
     *
     * MultiAppStartup::CSS_PATHS_KEY  : An array of paths to the css files belonging to the themes assigned to
     *              each app in the apps directory. This array will be empty if there are no themes assigned to
     *              any apps, or if startup failed. Also note, paths to css files will only exist for the apps
     *              that started up successfully.
     *
     * MultiAppStartup::JS_PATHS_KEY : An array of paths to the javascript files belonging to the javascript
     *                                 libraries assigned to each app in the apps directory. This array will
     *                                 be empty if there are no javascript libraries assigned to any apps,
     *                                 or if startup failed. Also note, paths to javascript files will only
     *                                 exist for the apps that started up successfully.
     * @return array An array of paths available to this object.
     * @see MultiAppStartup::getCssPaths()
     * @see MultiAppStartup::getJsPaths()
     */
    public function getPaths(): array
    {
        return array(
            self::APP_DIR_PATH_KEY => $this->appDirPath,
            self::APP_CONFIG_PATHS_KEY => $this->appConfigPaths,
            self::CSS_PATHS_KEY => $this->getCssPaths(),
            self::JS_PATHS_KEY => $this->getJsPaths(),
        );
    }

    /**
     * Handles startup logic. Determines the paths to the IAppConfig implementations, and the namespaces of each
     * app in the apps directory. Instantiates each app's IAppConfig implementation, and an instantiates an
     * IAppStartup implementation for each app in the apps directory. Calls the startup() method of each IAppStartup
     * implementation assigned to the $appStartupObjects property's array
     * @return bool True if each app started up successfully, false otherwise.
     * @see MultiAppStartup::setAppConfigPaths()
     * @see MultiAppStartup::setAppNamespaces()
     * @see MultiAppStartup::setAppConfigObjects()
     * @see MultiAppStartup::setAppStartupObjects()
     * @see IAppStartup::startup()
     */
    public function startup(): bool
    {
        $status = array();
        foreach ($this->appStartupObjects as $startupObject) {
            array_push($status, $startupObject->startup());
        }
        return !in_array(false, $status, true);
    }

    /**
     * Instantiates each app in the apps directory's DarlingCms\interfaces\accessControl\IAppConfig implementation.
     *
     * WARNING: This method will not perform instantiation, and will log an error for any apps that do not define
     * an AppConfig class, or any app's that define an AppConfig class that does not implement the
     * DarlingCms\interfaces\accessControl\IAppConfig interface. Consequently, any app that does not define an
     * AppConfig class, or defines a AppConfig class that does not implement the
     * DarlingCms\interfaces\accessControl\IAppConfig interface, will be excluded from the startup process.
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
            error_log('Darling Cms Startup Error: Failed to start app ' . $appName . '. The ' . $appName . ' app\'s AppConfig.php file does not define an implementation of the DarlingCms\interfaces\accessControl\IAppConfig interface.');
        }
    }

    /**
     * Instantiates an IAppStartup implementation for each app in the apps directory.
     * @see IAppStartup
     */
    private function setAppStartupObjects(): void
    {
        foreach ($this->appConfigObjects as $appConfigObject) {
            switch ($this->startupMode) {
                case self::STARTUP_DEFAULT:
                    var_dump('using no cache');
                    array_push($this->appStartupObjects, new AppStartup($appConfigObject));
                    break;
                case self::STARTUP_JSON_CACHE:
                    var_dump('using json cache');
                    array_push($this->appStartupObjects, new AppStartupJsonCache($appConfigObject));
                    break;
            }
        }
    }

    /**
     * Determines the path to each app's AppConfig.php file, and assigns it to the the $appConfigPaths property's array.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's array for any apps that do not
     * provide an AppConfig.php file. Furthermore, this method will log an error for any apps that do not provide
     * an AppConfig.php file. Consequently, any apps that do not provide an AppConfig.php file will be excluded
     * from the startup process.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's array for any apps that are
     * assigned to the $excludedApps property's array. Consequently, any apps that are assigned to the $excludedApps
     * property's array will be excluded from the startup process.
     *
     * @see \DirectoryIterator
     * @see \DirectoryIterator::getRealPath()
     * @see \DirectoryIterator::isDir()
     * @see \DirectoryIterator::isDot()
     */
    private function setAppConfigPaths(): void
    {
        $appDirIterator = new \DirectoryIterator($this->appDirPath);
        foreach ($appDirIterator as $directoryIterator) {
            $appConfigPath = $directoryIterator->getRealPath() . '/AppConfig.php';
            if (in_array($directoryIterator->getFilename(), $this->excludedApps, true) === false && $directoryIterator->isDir() === true && $directoryIterator->isDot() === false) {
                switch (file_exists($appConfigPath)) {
                    case false:
                        error_log('Darling Cms Startup Error: Failed to start app ' . $directoryIterator->getFilename() . '. The app does not provide an AppConfig.php file.');
                        break;
                    default:
                        array_push($this->appConfigPaths, $appConfigPath);
                        break;
                }
            }
        }
    }

    /**
     * Exclude specified app(s) from the startup process.
     * @param string ...$appName The name of the app to exclude from startup. To set more then one app to be
     *                           excluded from startup pass additional app names as additional parameters.
     */
    public function excludeApp(string ...$appName): void
    {
        foreach ($appName as $app) {
            array_push($this->excludedApps, $app);
        }
    }

    /**
     * Determines each app's namespace and assigns it to the $appNamespaces property's array.
     */
    private function setAppNamespaces(): void
    {
        foreach ($this->appConfigPaths as $appConfigPath) {
            array_push($this->appNamespaces, '\\' . ucfirst(str_replace('/', '\\', str_replace(array(str_replace('core/classes/startup', '', __DIR__), 'AppConfig.php'), '', $appConfigPath))));
        }
    }

    /**
     * Handles shutdown logic. Specifically, calls the shutdown() method of each IAppStartup implementation
     * assigned to the $appStartupObjects property's array.
     * @return bool True if all apps shutdown successfully, false otherwise.
     * @see IAppStartup::shutdown()
     */
    public function shutdown(): bool
    {
        $status = array();
        foreach ($this->appStartupObjects as $startupObject) {
            array_push($status, $startupObject->shutdown());
        }
        return !in_array(false, $status, true);
    }

    /**
     * Handles restart logic. Specifically, calls the restart() method of each IAppStartup implementation
     * assigned to the $appStartupObjects property's array.
     * @return bool True if all apps restarted successfully, false otherwise.
     * @see IAppStartup::restart()
     */
    public function restart(): bool
    {
        $status = array();
        foreach ($this->appStartupObjects as $startupObject) {
            array_push($status, $startupObject->restart());
        }
        return !in_array(false, $status, true);
    }
}
