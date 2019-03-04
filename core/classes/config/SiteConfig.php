<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-03-04
 * Time: 08:37
 */

namespace DarlingCms\classes\config;


use DarlingCms\interfaces\config\ISiteConfig;

/**
 * Class SiteConfig. This class can be used to define a site's configuration settings.
 * Note: This class just serves as a representation of a site's configuration settings,
 * it is NOT responsible for creating or modifying a site's configuration file.
 * @package DarlingCms\classes\config
 */
class SiteConfig implements ISiteConfig
{
    /**
     * @var string $coreDBName Name of the database used by Core.
     */
    private $coreDBName;

    /**
     * @var string $appsDBName Name of the database used by Apps.
     */
    private $appsDBName;

    /**
     * @var string $usersDBName Name of the database used to store User data.
     */
    private $usersDBName;

    /**
     * @var string $passwordsDBName Name of the database used to store Password data.
     */
    private $passwordsDBName;

    /**
     * @var string $privilegesDBName Name of the database used to store privileges,
     *                               i.e., Actions, Permissions, and Roles.
     */
    private $privilegesDBName;

    /**
     * @var string $dbHost Name of the database host.
     */
    private $dbHost;

    /**
     * @var string $coreDBUsername The Core database user's username.
     */
    private $coreDBUsername;

    /**
     * @var string $coreDBPassword The Core database user's password.
     */
    private $coreDBPassword;

    /**
     * @var string $appsDBUsername The Apps database user's username.
     */
    private $appsDBUsername;

    /**
     * @var string $appsDBPassword The Apps database user's password.
     */
    private $appsDBPassword;

    /**
     * @var string $usersDBUsername The Users database user's username.
     */
    private $usersDBUsername;

    /**
     * @var string $usersDBPassword The Users database user's password.
     */
    private $usersDBPassword;

    /**
     * @var string $passwordsDBUsername The Passwords database user's username.
     */
    private $passwordsDBUsername;

    /**
     * @var string $passwordsDBPassword The Passwords database user's password.
     */
    private $passwordsDBPassword;

    /**
     * @var string $privilegesDBUsername The Privileges database user's username.
     */
    private $privilegesDBUsername;

    /**
     * @var string $privilegesDBPassword The Privileges database user's password.
     */
    private $privilegesDBPassword;

    /**
     * Return the name of the database used by Core
     * @return string The name of the database used by Core.
     */
    public function getCoreDBName(): string
    {
        return $this->coreDBName;
    }

    /**
     * Return the name of the database used by Apps
     * @return string The name of the database used by Apps.
     */
    public function getAppsDBName(): string
    {
        return $this->appsDBName;
    }

    /**
     * Return the name of the database used to store User data.
     * @return string The name of the database used to store User data.
     */
    public function getUsersDBName(): string
    {
        return $this->usersDBName;
    }

    /**
     * Return the name of the database used to store Password data.
     * @return string The name of the database used to store Password data.
     */
    public function getPasswordsDBName(): string
    {
        return $this->passwordsDBName;
    }

    /**
     * Return the name of the database used to store privileges, i.e., Actions, Permissions, Roles.
     * @return string The name of the database used to store privileges, i.e., Actions, Permissions, Roles
     */
    public function getPrivilegesDBName(): string
    {
        return $this->privilegesDBName;
    }

    /**
     * Returns the database host.
     * @return string The database host.
     */
    public function getDBHostName(): string
    {
        return $this->dbHost;
    }

    /**
     * Returns the Core database user's username.
     * @return string The Core database user's username.
     */
    public function getCoreDBUserName(): string
    {
        return $this->coreDBUsername;
    }

    /**
     * Returns the Core database user's password.
     * @return string The Core database user's password.
     */
    public function getCoreDBPassword(): string
    {
        return $this->coreDBPassword;
    }

    /**
     * Returns the Apps database user's username.
     * @return string The Apps database user's username.
     */
    public function getAppsDBUserName(): string
    {
        return $this->appsDBUsername;
    }

    /**
     * Returns the Apps database user's password.
     * @return string The Apps database user's password.
     */
    public function getAppsDBPassword(): string
    {
        return $this->appsDBPassword;
    }

    /**
     * Returns the Users database user's username.
     * @return string The Users database user's username.
     */
    public function getUsersDBUserName(): string
    {
        return $this->usersDBUsername;
    }

    /**
     * Returns the Users database user's password.
     * @return string The Users database user's password.
     */
    public function getUsersDBPassword(): string
    {
        return $this->usersDBPassword;
    }

    /**
     * Returns the Passwords database user's username.
     * @return string The Passwords database user's username.
     */
    public function getPasswordsDBUserName(): string
    {
        return $this->passwordsDBUsername;
    }

    /**
     * Returns the Passwords database user's password.
     * @return string The Passwords database user's password.
     */
    public function getPasswordsDBPassword(): string
    {
        return $this->passwordsDBPassword;
    }

    /**
     * Returns the Privileges database user's username.
     * @return string The Privileges database user's username.
     */
    public function getPrivilegesDBUserName(): string
    {
        return $this->privilegesDBUsername;
    }

    /**
     * Returns the Privileges database user's password.
     * @return string The Privileges database user's password.
     */
    public function getPrivilegesDBPassword(): string
    {
        return $this->privilegesDBPassword;
    }

    /**
     * Set the name of the database used by Core.
     * @param string $coreDBName The name of the database used by Core.
     */
    public function setCoreDBName(string $coreDBName): void
    {
        $this->coreDBName = $coreDBName;
    }

    /**
     * Set the name of the database used by Apps.
     * @param string $appsDBName The name of the database used by Apps.
     */
    public function setAppsDBName(string $appsDBName): void
    {
        $this->appsDBName = $appsDBName;
    }

    /**
     * Set the name of the database used to store User data.
     * @param string $usersDBName The name of the database used to store User data.
     */
    public function setUsersDBName(string $usersDBName): void
    {
        $this->usersDBName = $usersDBName;
    }

    /**
     * Set the name of the database used to store Password data.
     * @param string $passwordsDBName The name of the database used to store Password data.
     */
    public function setPasswordsDBName(string $passwordsDBName): void
    {
        $this->passwordsDBName = $passwordsDBName;
    }

    /**
     * Set the name of the database used to store Privileges, i.e., Actions, Permissions, and Roles.
     * @param string $privilegesDBName The name of the database used to store Privileges,  i.e.,
     *                                 Actions, Permissions, and Roles.
     */
    public function setPrivilegesDBName(string $privilegesDBName): void
    {
        $this->privilegesDBName = $privilegesDBName;
    }

    /**
     * Set the name of the database host.
     * @param string $dbHost The name of the database host.
     */
    public function setDbHost(string $dbHost): void
    {
        $this->dbHost = $dbHost;
    }

    /**
     * Set the Core database user's username.
     * @param string $coreDBUsername The Core database user's user name.
     */
    public function setCoreDBUsername(string $coreDBUsername): void
    {
        $this->coreDBUsername = $coreDBUsername;
    }

    /**
     * Set the Core database user's password.
     * @param string $coreDBPassword The Core database user's password.
     */
    public function setCoreDBPassword(string $coreDBPassword): void
    {
        $this->coreDBPassword = $coreDBPassword;
    }

    /**
     * Set the Apps database user's username.
     * @param string $appsDBUsername The Apps database user's username.
     */
    public function setAppsDBUsername(string $appsDBUsername): void
    {
        $this->appsDBUsername = $appsDBUsername;
    }

    /**
     * Set the Apps database user's password.
     * @param string $appsDBPassword The Apps database user's password.
     */
    public function setAppsDBPassword(string $appsDBPassword): void
    {
        $this->appsDBPassword = $appsDBPassword;
    }

    /**
     * Set the Users database user's username.
     * @param string $usersDBUsername The Users database user's username.
     */
    public function setUsersDBUsername(string $usersDBUsername): void
    {
        $this->usersDBUsername = $usersDBUsername;
    }

    /**
     * Set the Users database user's password.
     * @param string $usersDBPassword The Users database user's password.
     */
    public function setUsersDBPassword(string $usersDBPassword): void
    {
        $this->usersDBPassword = $usersDBPassword;
    }

    /**
     * Set the Passwords database user's username.
     * @param string $passwordsDBUsername The Passwords database user's username.
     */
    public function setPasswordsDBUsername(string $passwordsDBUsername): void
    {
        $this->passwordsDBUsername = $passwordsDBUsername;
    }

    /**
     * Set the Passwords database user's password.
     * @param string $passwordsDBPassword The Passwords database user's password.
     */
    public function setPasswordsDBPassword(string $passwordsDBPassword): void
    {
        $this->passwordsDBPassword = $passwordsDBPassword;
    }

    /**
     * Set the Privileges database user's username.
     * @param string $privilegesDBUsername The Privileges database user's username.
     */
    public function setPrivilegesDBUsername(string $privilegesDBUsername): void
    {
        $this->privilegesDBUsername = $privilegesDBUsername;
    }

    /**
     * Set the Privileges database user's password.
     * @param string $privilegesDBPassword The Privileges database user's password.
     */
    public function setPrivilegesDBPassword(string $privilegesDBPassword): void
    {
        $this->privilegesDBPassword = $privilegesDBPassword;
    }
}
