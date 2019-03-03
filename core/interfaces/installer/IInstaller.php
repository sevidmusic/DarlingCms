<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-02
 * Time: 22:23
 */

namespace DarlingCms\interfaces\installer;


/**
 * Class IInstaller. Defines the basic contract of an object whose responsibility is to perform installation
 * and un-installation.
 * @package DarlingCms\interfaces\installer
 */
interface IInstaller
{
    /**
     * Perform installation.
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool;

    /**
     * Perform un-installation.
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool;

}
