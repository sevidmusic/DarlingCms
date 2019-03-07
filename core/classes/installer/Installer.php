<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-05
 * Time: 11:15
 */

namespace DarlingCms\classes\installer;

use DarlingCms\classes\staticClasses\utility\StringUtility;
use DarlingCms\interfaces\installer\IInstaller;
use SplObjectStorage;

/**
 * Class Installer. Defines an implementation of the IInstaller interface that can be used
 * to run multiple IInstaller implementation instances.
 * @package DarlingCms\classes\installer
 */
class Installer implements IInstaller
{

    /**
     * @var SplObjectStorage $installers Object map of IInstaller implementations run by this Installer.
     */
    private $installers;

    /**
     * Installer constructor. Instantiates the SplObjectStorage() instance used to store the IInstaller
     * implementation instances run by this Installer and attaches the supplied IInstaller implementation
     * instances.
     * @param IInstaller ...$installers The IInstaller implementation instances
     *                                  this Installer will run.
     */
    public function __construct(IInstaller ...$installers)
    {
        $this->installers = new SplObjectStorage();
        foreach ($installers as $installer) {
            $this->installers->attach($installer);
        }
    }

    /**
     * Perform installation. This method will return true if all of the
     * attached IInstaller implementation instance's install() methods
     * run successfully, false otherwise.
     *
     * Note: This method will call the install() method of all IInstaller
     * implementation instances attached to this Installer.
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool
    {
        $status = array();
        foreach ($this->installers as $installer) {
            $installerName = StringUtility::convertFromCamelCase(str_replace('DarlingCms\\classes\\installer\\', '', get_class($installer)));
            // check that installer is in fact an implementation of the IInstaller interface
            if (in_array('DarlingCms\interfaces\installer\IInstaller', class_implements($installer), true) === false) {
                error_log('Installer Error: Unsupported installer ' . $installerName . '.');
                continue;
            }
            if ($installer->install() === false) {
                array_push($status, false);
                error_log('Installer Error (install error): The ' . $installerName . ' failed.');
            }
        }
        return in_array(false, $status, true) === false;
    }

    /**
     * Perform un-installation. This method will return true if all of the
     * attached IInstaller implementation instance's uninstall() methods
     * run successfully, false otherwise.
     * Note: This method will call the uninstall() method of all IInstaller
     * implementation instances attached to this Installer.
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        $status = array();
        foreach ($this->installers as $installer) {
            $installerName = StringUtility::convertFromCamelCase(str_replace('DarlingCms\\classes\\installer\\', '', get_class($installer)));
            // check that installer is in fact an implementation of the IInstaller interface
            if (in_array('DarlingCms\interfaces\installer\IInstaller', class_implements($installer), true) === false) {
                error_log('Installer Error: Unsupported installer ' . $installerName . '.');
                continue;
            }
            if ($installer->unInstall() === false) {
                array_push($status, false);
                error_log('Installer Error (un-install error): The ' . $installerName . ' failed.');
            }
        }
        return in_array(false, $status, true) === false;
    }
}
