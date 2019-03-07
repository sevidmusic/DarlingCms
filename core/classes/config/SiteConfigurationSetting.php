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
     * Returns the setting's name.
     * @return string The setting's name.
     */
    public function getSettingName(): string
    {
        return 'devSettingName';
    }

    /**
     * Returns the setting's value.
     * @return string The setting's value.
     */
    public function getSettingValue(): string
    {
        return 'devSettingValue';
    }

    /**
     * Returns the setting's description.
     * @return string The setting's description.
     */
    public function getDescription(): string
    {
        return 'Test configuration description';
    }

}
