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
 */
class MultiAppStartup implements IAppStartup
{
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
     * MultiAppStartup constructor. Determines the path to the Darling Cms apps directory and assigns it to the
     * $appDirPath property.
     */
    public function __construct()
    {
        $this->appDirPath = str_replace('core/classes/startup', 'apps', __DIR__);
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
        return $cssPaths;
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
        return $jsPaths;
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
     * Returns an array of the following paths: The path to the Darling Cms apps directory, an array of paths to
     * the AppConfig.php files defined by each of the apps in the apps directory, an array of paths to the css files
     * belonging to the themes assigned to each app in the apps directory, an array of paths to the javascript
     * files belonging to the javascript libraries assigned to each app in the apps directory.
     *
     * The paths are indexed by the following indexes:
     *
     * 'appDirPath' : The path to the apps directory.
     *
     * 'appConfigPaths' : An array of paths to the AppConfig.php files defined by each app in the apps directory.
     *
     * 'cssPaths' : An array of paths to the css files belonging to the themes assigned to each app in the
     *              apps directory. This array will be empty if there are no themes assigned to any apps,
     *              or if startup failed. Also note, paths to css files will only exist for the apps that
     *              started up successfully.
     *
     * 'jsPaths' : An array of paths to the javascript files belonging to the javascript libraries assigned
     *             to each app in the apps directory. This array will be empty if there are no javascript
     *             libraries assigned to any apps, or if startup failed. Also note, paths to javascript files
     *             will only exist for the apps that started up successfully.
     *
     * @return array An array of paths available to this object.
     * @see MultiAppStartup::getCssPaths()
     * @see MultiAppStartup::getJsPaths()
     */
    public function getPaths(): array
    {
        return array(
            'appDirPath' => $this->appDirPath,
            'appConfigPaths' => $this->appConfigPaths,
            'cssPaths' => $this->getCssPaths(),
            'jsPaths' => $this->getJsPaths(),
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
        $this->setAppConfigPaths();
        $this->setAppNamespaces();
        $this->setAppConfigObjects();
        $this->setAppStartupObjects();
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
            array_push($this->appStartupObjects, new AppStartup($appConfigObject));
        }
    }

    /**
     * Determines the path to each app's AppConfig.php file, and assigns it to the the $appConfigPaths property's array.
     *
     * WARNING: This method will not assign a path to the $appConfigPaths property's array for any apps that do not
     * provide an AppConfig.php file. Furthermore, this method will log an error for any apps that do not provide
     * an AppConfig.php file. Consequently, any apps that do not provide an AppConfig.php file will be excluded
     * from the startup process.
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
            if ($directoryIterator->isDir() === true && $directoryIterator->isDot() === false) {
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
