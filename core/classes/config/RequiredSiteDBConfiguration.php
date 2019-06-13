<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-17
 * Time: 23:47
 */

namespace DarlingCms\classes\config;


use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\config\IDBConfig;
use DarlingCms\interfaces\config\ISiteConfiguration;
use DarlingCms\interfaces\config\ISiteConfigurationSetting;

/**
 * Class RequiredSiteDBConfiguration. Defines an implementation of the ISiteConfiguration
 * interface that can be used to define the database configurations that are required
 * by all sites.
 * An instance of this class will represent the site configuration settings for the following databases:
 * - The database used by Core
 * - The database used by Apps
 * - The database used to store User data
 * - The database used to store Password data
 * - The database used to store Privileges, i.e. Actions, Permissions, and Roles
 * @package DarlingCms\classes\config
 */
class RequiredSiteDBConfiguration implements ISiteConfiguration
{
    /**
     * @var string Constant that defines the index used to identify the IDBConfig implementation
     *             instance for the database used by Core in the $dbConfigs property's
     *             array.
     */
    private const CORE_DB_INDEX = 'CORE';

    /**
     * @var string Constant that defines the index used to identify the IDBConfig implementation
     *             instance for the database used by Apps in the $dbConfigs property's
     *             array.
     */
    private const APPS_DB_INDEX = 'APPS';

    /**
     * @var string Constant that defines the index used to identify the IDBConfig implementation
     *             instance for the database used to store User data in the $dbConfigs
     *             property's array.
     */
    private const USERS_DB_INDEX = 'USERS';

    /**
     * @var string Constant that defines the index used to identify the IDBConfig implementation
     *             instance for the database used to store Password data in the $dbConfigs
     *             property's array.
     */
    private const PASSWORDS_DB_INDEX = 'PASSWORDS';

    /**
     * @var string Constant that defines the index used to identify the IDBConfig implementation
     *             instance for the database used to store Privileges in the $dbConfigs
     *             property's array.
     */
    private const PRIVILEGES_DB_INDEX = 'PRIVILEGES';

    /**
     * @var array|IDBConfig[] Array of the IDBConfig implementations for the Core, Apps, Users,
     * Passwords, and Privileges database configurations.
     */
    private $dbConfigs;

    /**
     * SiteDBConfiguration constructor. Adds the the required database configurations.
     * @param IDBConfig $coreDBConfig The IDBConfig implementations instance for the database used by Core.
     * @param IDBConfig $appsDBConfig The IDBConfig implementations instance for the database used by Apps.
     * @param IDBConfig $usersDBConfig The IDBConfig implementations instance for the database used to store
     *                                 User data.
     * @param IDBConfig $passwordsDBConfig The IDBConfig implementations instance for the database used to store
     *                                 Password data.
     * @param IDBConfig $privilegesDBConfig The IDBConfig implementations instance for the database used to store
     *                                 Privileges, i.e., Actions, Permissions, and Roles.
     */
    public function __construct(IDBConfig $coreDBConfig, IDBConfig $appsDBConfig, IDBConfig $usersDBConfig, IDBConfig $passwordsDBConfig, IDBConfig $privilegesDBConfig)
    {
        $this->dbConfigs = array(
            self::CORE_DB_INDEX => $coreDBConfig,
            self::APPS_DB_INDEX => $appsDBConfig,
            self::USERS_DB_INDEX => $usersDBConfig,
            self::PASSWORDS_DB_INDEX => $passwordsDBConfig,
            self::PRIVILEGES_DB_INDEX => $privilegesDBConfig,
        );
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
        $configSettings = array();
        if (count($this->dbConfigs) === 5) {
            foreach ($this->dbConfigs as $index => $dbConfig) {
                switch ($index) {
                    case self::CORE_DB_INDEX:
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::CORE_DB_NAME_SETTING, $dbConfig->getDBName(), 'The name of the database used by Core.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::CORE_DB_HOST_SETTING, $dbConfig->getDBHostName(), 'The host of the database used by Core.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::CORE_DB_USER_NAME_SETTING, $dbConfig->getDBUsername(), 'The username for the database used by Core.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::CORE_DB_PASSWORD_SETTING, $dbConfig->getDBUserPassword(), 'The user password for the database used by Core.'));
                        break;
                    case self::APPS_DB_INDEX:
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::APPS_DB_NAME_SETTING, $dbConfig->getDBName(), 'The name of the database used by Apps.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::APPS_DB_HOST_SETTING, $dbConfig->getDBHostName(), 'The host of the database used by Apps.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::APPS_DB_USER_NAME_SETTING, $dbConfig->getDBUsername(), 'The username for the database used by Apps.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::APPS_DB_PASSWORD_SETTING, $dbConfig->getDBUserPassword(), 'The user password for the database used by Apps.'));
                        break;
                    case self::USERS_DB_INDEX:
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::USERS_DB_NAME_SETTING, $dbConfig->getDBName(), 'The name of the database used to store Users.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::USERS_DB_HOST_SETTING, $dbConfig->getDBHostName(), 'The host of the database used to store Users.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::USERS_DB_USER_NAME_SETTING, $dbConfig->getDBUsername(), 'The username for the database used to store Users.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::USERS_DB_PASSWORD_SETTING, $dbConfig->getDBUserPassword(), 'The user password for the database used to store Users.'));
                        break;
                    case self::PASSWORDS_DB_INDEX:
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PASSWORDS_DB_NAME_SETTING, $dbConfig->getDBName(), 'The name of the database used to store Passwords.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PASSWORDS_DB_HOST_SETTING, $dbConfig->getDBHostName(), 'The host of the database used to store Passwords.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PASSWORDS_DB_USER_NAME_SETTING, $dbConfig->getDBUsername(), 'The username for the database used to store Passwords'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PASSWORDS_DB_PASSWORD_SETTING, $dbConfig->getDBUserPassword(), 'The user password for the database used to store Passwords.'));
                        break;
                    case self::PRIVILEGES_DB_INDEX:
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PRIVILEGES_DB_NAME_SETTING, $dbConfig->getDBName(), 'The name of the database used to store Privileges, i.e., Actions, Permissions, and Roles.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PRIVILEGES_DB_HOST_SETTING, $dbConfig->getDBHostName(), 'The host of the database used to store Privileges, i.e., Actions, Permissions, and Roles.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PRIVILEGES_DB_USER_NAME_SETTING, $dbConfig->getDBUsername(), 'The username for the database used to store Privileges, i.e., Actions, Permissions, and Roles.'));
                        array_push($configSettings, new SiteConfigurationSetting(CoreValues::PRIVILEGES_DB_PASSWORD_SETTING, $dbConfig->getDBUserPassword(), 'The user password for the database used to store Privileges, i.e., Actions, Permissions, and Roles.'));
                        break;
                }
            }
        }
        return $configSettings;
    }

    /**
     * Returns the name used to identify this site configuration.
     * @return string The name used to identify this site configuration.
     */
    public function getConfigurationName(): string
    {
        return 'Required Database Configurations';
    }

    /**
     * Returns a description of this site configuration.
     * @return string A description of this site configuration.
     */
    public function getConfigurationDescription(): string
    {
        return 'The following database configurations MUST be defined by all sites. WARNING: The following site configuration\'s settings are required, DO NOT edit them unless you know exactly what you are doing, and you are aware of the consequences!';
    }

    /**
     * Remove a site configuration setting from this site configuration.
     * @param string $settingName The name of the setting to remove.
     */
    public function removeSetting(string $settingName): void
    {
        // TODO: Implement removeSetting() method. NOTE: This method will be defined by the ASiteConfiguration abstract class which has not yet been developed
    }

    /**
     * Add a site configuration setting to this site configuration.
     * @param ISiteConfigurationSetting $siteConfigurationSetting The ISiteConfigurationSetting implementation
     *                                                            instance that represents the site configuration
     *                                                            setting.
     */
    public function addSetting(ISiteConfigurationSetting $siteConfigurationSetting): void
    {
        // TODO: Implement removeSetting() method. NOTE: This method will be defined by the ASiteConfiguration abstract class which has not yet been developed
    }


}
