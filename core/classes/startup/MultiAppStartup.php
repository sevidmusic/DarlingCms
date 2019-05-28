<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 4/29/18
 * Time: 11:03 PM
 */

namespace DarlingCms\classes\startup;

use DarlingCms\classes\info\AppInfo;
use DarlingCms\interfaces\startup\IAppStartup;

/**
 * Class MultiAppStartup. Defines an implementation of the IAppStartup interface that
 * starts up multiple Darling Cms apps via the IAppStartup implementation instances
 * assigned to the injected AppInfo implementation instance.
 *
 * @package DarlingCms\classes\startup
 *
 * @see MultiAppStartup::getCssPaths()
 * @see MultiAppStartup::getJsPaths()
 * @see MultiAppStartup::getAppOutput()
 * @see MultiAppStartup::getPaths()
 * @see MultiAppStartup::startup()
 * @see MultiAppStartup::shutdown()
 * @see MultiAppStartup::restart()
 */
class MultiAppStartup implements IAppStartup
{
    /**
     * @var string Index assigned to the value of the path to the apps directory in the
     *             $paths property's array.
     */
    const APP_DIR_PATH_INDEX = 'appDirPath';

    /**
     * @var string Index assigned to the array of paths to the respective AppConfig.php
     *             files in the $paths property's array.
     */
    const APP_CONFIG_PATHS_INDEX = 'appConfigPaths';

    /**
     * @var string Value of the index assigned to the array of css paths in the $paths
     *             property's array.
     */
    const CSS_PATHS_INDEX = 'cssPaths';

    /**
     * @var string Value of the index assigned to the array of js paths in the $paths
     *             property's array.
     */
    const JS_PATHS_INDEX = 'jsPaths';

    /**
     * @var AppInfo Instance of an AppInfo implementation instance that provides
     *              information about the apps handled by this MultiAppStartup
     *              implementation instance.
     */
    private $appInfo;

    /**
     * MultiAppStartup constructor. Injects the AppInfo implementation instance
     * that provides information about the apps handled by this MultiAppStartup
     * implementation instance.
     *
     * @param AppInfo $appInfo The AppInfo implementation instance that provides
     *                         information about the apps handled by this
     *                         MultiAppStartup implementation instance.
     */
    public function __construct(AppInfo $appInfo)
    {
        $this->appInfo = $appInfo;
    }

    /**
     * Returns this MultiAppStartup instance's AppInfo instance.
     *
     * @return AppInfo This MultiAppStartup instance's AppInfo instance.
     */
    public function getAppInfo(): AppInfo
    {
        return $this->appInfo;
    }

    /**
     * Returns an array of paths to the css files belonging to the themes assigned to
     * each app that started up successfully.
     *
     * @return array Array of paths to the css files belonging to the themes assigned
     * to each app that started up successfully.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::getCssPaths()
     */
    public function getCssPaths(): array
    {
        $cssPaths = array();
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance
         *                                 being processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            foreach ($startupObject->getCssPaths() as $cssPath) {
                array_push($cssPaths, $cssPath);
            }
        }
        return array_unique($cssPaths);
    }

    /**
     * Returns an array of paths to the javascript files belonging to the javascript
     * libraries assigned to each app that started up successfully.
     *
     * @return array Array of paths to the javascript files belonging to the javascript
     * libraries assigned to each app that started up successfully.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::getJsPaths()
     */
    public function getJsPaths(): array
    {
        $jsPaths = array();
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance being processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            foreach ($startupObject->getJsPaths() as $jsPath) {
                array_push($jsPaths, $jsPath);
            }
        }
        return array_unique($jsPaths);
    }

    /**
     * Returns the collective output of the apps that started up successfully.
     *
     * @return string The collective output of the apps that started up successfully.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::getAppOutput()
     */
    public function getAppOutput(): string
    {
        $output = '';
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance being
         *                                 processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            $output .= $startupObject->getAppOutput();
        }
        return $output;
    }

    /**
     * Returns an array of the following paths: The path to the Darling Cms apps directory,
     * an array of paths to the AppConfig.php files defined by each of the apps in the apps
     * directory, an array of paths to the css files belonging to the themes assigned to
     * each app in the apps directory, an array of paths to the javascript files belonging
     * to the javascript libraries assigned to each app in the apps directory.
     *
     * The paths are indexed using the class constants defined for each index:
     *
     * MultiAppStartup::APP_DIR_PATH_KEY : The path to the apps directory.
     *
     * MultiAppStartup::APP_CONFIG_PATHS_KEY : An array of paths to the AppConfig.php
     *                                         files defined by each app in the apps
     *                                         directory.
     *
     * MultiAppStartup::CSS_PATHS_KEY  : An array of paths to the css files belonging
     *                                   to the themes assigned to each app in the apps
     *                                   directory. This array will be empty if there
     *                                   are no themes assigned to any apps, or if
     *                                   startup failed. Also note, paths to css files
     *                                   will only exist for the apps that started up
     *                                   successfully.
     *
     * MultiAppStartup::JS_PATHS_KEY : An array of paths to the javascript files
     *                                 belonging to the javascript libraries
     *                                 assigned to each app in the apps directory.
     *                                 This array will be empty if there are no
     *                                 javascript libraries assigned to any apps,
     *                                 or if startup failed. Also note, paths to
     *                                 javascript files will only exist for the
     *                                 apps that started up successfully.
     *
     * @return array An array of paths available to this object.
     *
     * @see AppInfo::getAppDirPath()
     * @see AppInfo::getAppConfigPaths()
     * @see MultiAppStartup::getCssPaths()
     * @see MultiAppStartup::getJsPaths()
     */
    public function getPaths(): array
    {
        return array(
            self::APP_DIR_PATH_INDEX => $this->appInfo->getAppDirPath(),
            self::APP_CONFIG_PATHS_INDEX => $this->appInfo->getAppConfigPaths(),
            self::CSS_PATHS_INDEX => $this->getCssPaths(),
            self::JS_PATHS_INDEX => $this->getJsPaths(),
        );
    }

    /**
     * Handles startup logic. Calls the startup() method of each IAppStartup
     * implementation provided by the injected AppInfo implementation instance.
     *
     * @return bool True if each app started up successfully, false otherwise.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::startup()
     */
    public function startup(): bool
    {
        $status = array();
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance being
         *                                 processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            array_push($status, $startupObject->startup());
        }
        return !in_array(false, $status, true);
    }

    /**
     * Handles shutdown logic. Specifically, calls the shutdown() method of each
     * IAppStartup implementation provided by the injected AppInfo implementation
     * instance.
     *
     * @return bool True if all apps shutdown successfully, false otherwise.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::shutdown()
     */
    public function shutdown(): bool
    {
        $status = array();
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance being
         *                                 processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            array_push($status, $startupObject->shutdown());
        }
        return !in_array(false, $status, true);
    }

    /**
     * Handles restart logic. Specifically, calls the restart() method of each
     * IAppStartup implementation provided by the injected AppInfo implementation
     * instance.
     *
     * @return bool True if all apps restarted successfully, false otherwise.
     *
     * @see AppInfo::getAppStartupObjects()
     * @see IAppStartup::restart()
     */
    public function restart(): bool
    {
        $status = array();
        /**
         * @var IAppStartup $startupObject The IAppStartup implementation instance being processed.
         */
        foreach ($this->appInfo->getAppStartupObjects() as $startupObject) {
            array_push($status, $startupObject->restart());
        }
        return !in_array(false, $status, true);
    }
}
