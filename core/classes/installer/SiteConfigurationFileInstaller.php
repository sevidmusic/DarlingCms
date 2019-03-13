<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-07
 * Time: 12:30
 */

namespace DarlingCms\classes\installer;

use DarlingCms\classes\config\SiteConfiguration;
use DarlingCms\classes\config\SiteConfigurationSetting;
use DarlingCms\classes\crud\SiteConfigurationFileCrud;
use DarlingCms\interfaces\config\ISiteConfiguration;
use DarlingCms\interfaces\installer\IInstaller;
use SplObjectStorage;

/**
 * Class SiteConfigurationFileInstaller. Defines an implementation of the IInstaller interface
 * that can be used to install and un-install site configurations in a site configuration file.
 * @package DarlingCms\classes\installer
 * @see IInstaller
 * @see SiteConfigurationSetting
 * @see SiteConfiguration
 * @see SiteConfigurationFileCrud
 */
class SiteConfigurationFileInstaller implements IInstaller
{
    /**
     * @var SiteConfigurationFileCrud The SiteConfigurationFileCrud implementation instance used
     *                                to create, read, update, and delete site configuration data
     *                                from the site configuration file.
     */
    private $siteConfigurationFileCrud;

    /**
     * @var SplObjectStorage|ISiteConfiguration[] Object map of the ISiteConfiguration implementation instances
     *                       that will be added to the site configuration file on install.
     */
    private $siteConfigurations;

    /**
     * SiteConfigurationFileInstaller constructor. Injects the SiteConfigurationFileCrud implementation
     * instance used to create, read, update, and delete site configuration data from the site configuration
     * file.
     * Attaches the provided ISiteConfiguration implementation instances to the internal object map of
     * ISiteConfiguration implementation instances that represent the site configurations that will be
     * added to the configuration file on install.
     * @param SiteConfigurationFileCrud $siteConfigurationFileCrud The SiteConfigurationFileCrud implementation
     *                                                             instance used to create, read, update, and
     *                                                             delete site configurations stored in the site
     *                                                             configuration file being installed.
     * @param ISiteConfiguration ...$siteConfigurations The ISiteConfiguration implementation instances that
     *                                                  represent the site configurations to install.
     * @see SiteConfigurationFileCrud
     * @see ISiteConfiguration
     * @see SplObjectStorage
     */
    public function __construct(SiteConfigurationFileCrud $siteConfigurationFileCrud, ISiteConfiguration ...$siteConfigurations)
    {
        $this->siteConfigurationFileCrud = $siteConfigurationFileCrud;
        $this->siteConfigurations = new SplObjectStorage();
        foreach ($siteConfigurations as $siteConfiguration) {
            $this->siteConfigurations->attach($siteConfiguration);
        }
    }

    /**
     * Perform installation. This method will first attempt to create the site configurations
     * in the site configuration file, if that fails, then it will attempt to update the site
     * configurations in case create failed because one or more of the site configurations
     * were already defined.
     * As long as either the attempts to create or update each of the site configurations
     * were successful, this method will return true, false otherwise.
     * @devNote update() is used as a fail safe if create() fails to insure a fresh install whenever
     *          this method is called.
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool
    {
        $status = array();
        foreach ($this->siteConfigurations as $siteConfiguration) {
            $configInstalled = ($this->siteConfigurationFileCrud->create($siteConfiguration) === true ? true : $this->siteConfigurationFileCrud->update($siteConfiguration->getConfigurationName(), $siteConfiguration));
            if ($configInstalled === false) {
                error_log('SiteConfigurationFileInstaller Error: Failed to install the  ' . $siteConfiguration->getConfigurationName() . ' configuration in site configuration file at ' . $this->siteConfigurationFileCrud->getSiteConfigurationFilePath());
            }
            array_push($status, $configInstalled);
        }
        if (in_array(false, $status, true) === true) {
            error_log('SiteConfigurationFileInstaller Error: Failed to complete installation of site configuration file at ' . $this->siteConfigurationFileCrud->getSiteConfigurationFilePath());
            /**
             * Call uninstall() to insure everything is cleaned up on failure
             */
            if ($this->uninstall() === false) {
                error_log('SiteConfigurationFileInstaller Error: Failed to clean up after install failed for site configuration stored at ' . $this->siteConfigurationFileCrud->getSiteConfigurationFilePath());
            }
            return false;
        }
        /**
         * All site configurations were installed successfully, return true.
         */
        return true;
    }

    /**
     * Perform un-installation.
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        if (file_exists($this->siteConfigurationFileCrud->getSiteConfigurationFilePath()) === true && unlink($this->siteConfigurationFileCrud->getSiteConfigurationFilePath()) === true) {
            return true;
        }
        error_log('SiteConfigurationFileInstaller Error: Failed to un-install site configuration file at ' . $this->siteConfigurationFileCrud->getSiteConfigurationFilePath());
        return false;
    }

}
