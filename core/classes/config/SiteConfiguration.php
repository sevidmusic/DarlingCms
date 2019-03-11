<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-07
 * Time: 17:28
 */

namespace DarlingCms\classes\config;


use DarlingCms\interfaces\config\ISiteConfiguration;
use DarlingCms\interfaces\config\ISiteConfigurationSetting;
use SplObjectStorage;

/**
 * Class SiteConfiguration. Defines an implementation of the ISiteConfiguration interface
 * that can be used to define a site configuration from a collection of ISiteConfigurationSetting
 * implementation instances.
 * @package DarlingCms\classes\config
 */
class SiteConfiguration implements ISiteConfiguration
{
    /**
     * @var string $siteConfigurationName The name that identifies this SiteConfiguration.
     */
    private $siteConfigurationName;

    /**
     * @var string $siteConfigurationDescription A description of this SiteConfiguration.
     */
    private $siteConfigurationDescription;

    /**
     * @var SplObjectStorage Object map of the ISiteConfigurationSetting implementation instances that
     *                       make up this SiteConfiguration.
     */
    private $siteConfigurationSettings;

    /**
     * SiteConfiguration constructor. Sets the SiteConfiguration's name, sets the SiteConfiguration's
     * description, and attaches the supplied ISiteConfiguration implementation instances.
     * @param string $siteConfigurationName A name to identify this SiteConfiguration
     * @param string $siteConfigurationDescription A description of this SiteConfiguration
     * @param ISiteConfigurationSetting ...$siteConfigurationSettings The ISiteConfigurationSetting
     *                                                                implementation instances to
     *                                                                associate with this SiteConfiguration.
     */
    public function __construct(string $siteConfigurationName, string $siteConfigurationDescription, ISiteConfigurationSetting ...$siteConfigurationSettings)
    {
        $this->siteConfigurationName = $siteConfigurationName;
        $this->siteConfigurationDescription = $siteConfigurationDescription;
        $this->siteConfigurationSettings = new SplObjectStorage();
        foreach ($siteConfigurationSettings as $siteConfigurationSetting) {
            $this->siteConfigurationSettings->attach($siteConfigurationSetting);
        }
    }


    /**
     * Returns an array of the ISiteConfigurationSetting implementation instances
     * that belong to this site configuration.
     * @return array|ISiteConfigurationSetting[] An array of the ISiteConfigurationSetting
     *                                           implementation instances that belong to this
     *                                           site configuration.
     */
    public function getConfigurationSettings(): array
    {
        $siteConfigurationSettings = array();
        foreach ($this->siteConfigurationSettings as $siteConfigurationSetting) {
            array_push($siteConfigurationSettings, $siteConfigurationSetting);
        }
        return $siteConfigurationSettings;
    }

    /**
     * Returns the name used to identify this site configuration.
     * @return string The name used to identify this site configuration.
     */
    public function getConfigurationName(): string
    {
        return $this->siteConfigurationName;
    }

    /**
     * Returns a description of this site configuration.
     * @return string A description of this site configuration.
     */
    public function getConfigurationDescription(): string
    {
        return $this->siteConfigurationDescription;
    }

    /**
     * Add a site configuration setting to this site configuration.
     * @param ISiteConfigurationSetting $siteConfigurationSetting The ISiteConfigurationSetting implementation
     *                                                            instance that represents the site configuration
     *                                                            setting.
     */
    // @todo ! add method to ISiteConfiguration interface
    public function addSetting(ISiteConfigurationSetting $siteConfigurationSetting): void
    {
        $this->siteConfigurationSettings->attach($siteConfigurationSetting);
    }

    /**
     * Remove a site configuration setting from this site configuration.
     * @param string $settingName The name of the setting to remove.
     */
    // @todo ! add method to ISiteConfiguration interface
    public function removeSetting(string $settingName): void
    {
        foreach ($this->getConfigurationSettings() as $siteConfigurationSetting) {
            if ($siteConfigurationSetting->getSettingName() === $settingName) {
                $this->siteConfigurationSettings->detach($siteConfigurationSetting);
            }
        }
    }

}
