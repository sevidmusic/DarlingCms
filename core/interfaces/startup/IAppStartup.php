<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 11:33 AM
 */

namespace DarlingCms\interfaces\startup;

use DarlingCms\interfaces\accessControl\IAppConfig;
use DarlingCms\interfaces\pathMap\IPathMap;

/**
 * Class IAppStartup. Defines the basic contract of an object that is
 * responsible for handling the startup, shutdown, and restart logic
 * of a Darling Cms app.
 *
 * @package DarlingCms\interfaces\startup
 *
 * @see IAppStartup::getCssPaths()
 * @see IAppStartup::getJsPaths()
 * @see IAppStartup::getAppOutput()
 * @see IStartup::startup()
 * @see IStartup::shutdown()
 * @see IStartup::restart()
 * @see IPathMap::getPaths()
 */
interface IAppStartup extends IStartup, IPathMap
{
    /**
     * Returns an array of paths to the css files assigned to the app.
     *
     * @return array Array of paths to the css files assigned to the app.
     */
    public function getCssPaths(): array;

    /**
     * Returns an array of paths to the javascript files assigned to the app.
     *
     * @return array Array of paths to the javascript files assigned to the app.
     */
    public function getJsPaths(): array;

    /**
     * Returns the app's output.
     *
     * @return string The app's output.
     */
    public function getAppOutput(): string;

    /**
     * Returns an instance of the app's IAppConfig implementation.
     *
     * @return IAppConfig An instance of the app's IAppConfig implementation.
     */
    public function getAppConfig(): IAppConfig;
}
