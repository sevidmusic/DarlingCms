<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-03
 * Time: 07:01
 */

namespace DarlingCms\interfaces\config;

/**
 * Interface ISiteConfiguration. Defines an object that can be used to organize
 * ISiteConfigurationSetting implementation instances into a site configuration.
 * @package DarlingCms\interfaces\config
 */
interface ISiteConfiguration
{
    /**
     * Returns an array of the ISiteConfigurationSetting implementation instances
     * that belong to this site configuration.
     * @return array|ISiteConfigurationSetting[] An array of the ISiteConfigurationSetting
     *                                           implementation instances that belong to this
     *                                           site configuration.
     */
    public function getConfigurationSettings(): array;

    /**
     * Returns the name used to identify this site configuration.
     * @return string The name used to identify this site configuration.
     */
    public function getConfigurationName(): string;

    /**
     * Returns a description of this site configuration.
     * @return string A description of this site configuration.
     */
    public function getConfigurationDescription(): string;

    /**
     * Remove a site configuration setting from this site configuration.
     * @param string $settingName The name of the setting to remove.
     */
    public function removeSetting(string $settingName): void;

    /**
     * Add a site configuration setting to this site configuration.
     * @param ISiteConfigurationSetting $siteConfigurationSetting The ISiteConfigurationSetting implementation
     *                                                            instance that represents the site configuration
     *                                                            setting.
     */
    public function addSetting(ISiteConfigurationSetting $siteConfigurationSetting): void;

}
