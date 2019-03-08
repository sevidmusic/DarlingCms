<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-07
 * Time: 17:20
 */

namespace DarlingCms\classes\config;


use DarlingCms\interfaces\config\ISiteConfigurationSetting;

/**
 * Class SiteConfigurationSetting. Defines an implementation of the ISiteConfigurationSetting
 * interface that can be used to define a single site configuration setting.
 * @package DarlingCms\classes\config
 */
class SiteConfigurationSetting implements ISiteConfigurationSetting
{
    /**
     * @var string $settingName The setting's name.
     */
    private $settingName;

    /**
     * @var string $settingValue The setting's value.
     */
    private $settingValue;

    /**
     * @var string $settingDescription The setting's description.
     */
    private $settingDescription;

    /**
     * SiteConfigurationSetting constructor. Sets the setting's name, the setting's value, and
     * the setting's description.
     * @param string $settingName The setting's name.
     * @param string $settingValue The setting's value.
     * @param string $settingDescription The setting's description.
     */
    public function __construct(string $settingName, string $settingValue, string $settingDescription)
    {
        $this->settingName = $settingName;
        $this->settingValue = $settingValue;
        $this->settingDescription = $settingDescription;
    }


    /**
     * Returns the setting's name.
     * @return string The setting's name.
     */
    public function getSettingName(): string
    {
        return $this->settingName;
    }

    /**
     * Returns the setting's value.
     * @return string The setting's value.
     */
    public function getSettingValue(): string
    {
        return $this->settingValue;
    }

    /**
     * Returns the setting's description.
     * @return string The setting's description.
     */
    public function getDescription(): string
    {
        return $this->settingDescription;
    }

}
