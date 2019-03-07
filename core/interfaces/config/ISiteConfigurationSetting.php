<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-03-06
 * Time: 21:41
 */

namespace DarlingCms\interfaces\config;

/**
 * Class ISiteConfigSetting. Defines the basic contract of an object that represents a single
 * site configuration setting.
 * @package DarlingCms\interfaces\config
 */
interface ISiteConfigurationSetting
{
    /**
     * Returns the setting's name.
     * @return string The setting's name.
     */
    public function getSettingName(): string;

    /**
     * Returns the setting's value.
     * @return string The setting's value.
     */
    public function getSettingValue(): string;

    /**
     * Returns the setting's description.
     * @return string The setting's description.
     */
    public function getDescription(): string;
}
