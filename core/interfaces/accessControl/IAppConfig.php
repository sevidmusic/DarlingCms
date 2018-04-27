<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 11:48 AM
 */

namespace DarlingCms\interfaces\accessControl;

/**
 * Interface IAppConfig. Defines the basic contract of an object that can be used to get the configuration settings
 * of a Darling Cms App.
 * @package DarlingCms\interfaces\accessControl
 * @see IAppConfig::getName()
 * @see IAppConfig::getThemeNames()
 * @see IAppConfig::getJsLibraryNames()
 * @see IAccessController::validateAccess()
 */
interface IAppConfig extends IAccessController
{
    /**
     * Gets the app's name.
     * @return string The app's name.
     */
    public function getName(): string;

    /**
     * Gets an array of the names of the themes assigned to the app.
     * @return array Array of the names of the themes assigned to the app.
     */
    public function getThemeNames(): array;

    /**
     * Gets an array of the names of the javascript libraries assigned to the app.
     * @return array Array of the names of the javascript libraries assigned to the app.
     */
    public function getJsLibraryNames(): array;
}
