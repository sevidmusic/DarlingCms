<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-03
 * Time: 07:01
 */

namespace DarlingCms\interfaces\config;

/**
 * Interface ISiteConfig. Defines the basic contract of an object that represents a site's configuration.
 * @package DarlingCms\interfaces\config
 */
interface ISiteConfig
{
    /**
     * Return the name of the database used by Core
     * @return string The name of the database used by Core.
     */
    public function getCoreDBName(): string;

    /**
     * Return the name of the database used by Apps
     * @return string The name of the database used by Apps.
     */
    public function getAppsDBName(): string;

    /**
     * Return the name of the database used to store User data.
     * @return string The name of the database used to store User data.
     */
    public function getUsersDBName(): string;

    /**
     * Return the name of the database used to store Password data.
     * @return string The name of the database used to store Password data.
     */
    public function getPasswordsDBName(): string;

    /**
     * Return the name of the database used to store privileges, i.e., Actions, Permissions, Roles.
     * @return string The name of the database used to store privileges, i.e., Actions, Permissions, Roles
     */
    public function getPrivilegesDBName(): string;

    /**
     * Returns the database host.
     * @return string The database host.
     */
    public function getDBHostName(): string;

    /**
     * Returns the Core database user's username.
     * @return string The Core database user's username.
     */
    public function getCoreDBUserName(): string;

    /**
     * Returns the Core database user's password.
     * @return string The Core database user's password.
     */
    public function getCoreDBPassword(): string;

    /**
     * Returns the Apps database user's username.
     * @return string The Apps database user's username.
     */
    public function getAppsDBUserName(): string;

    /**
     * Returns the Apps database user's password.
     * @return string The Apps database user's password.
     */
    public function getAppsDBPassword(): string;

    /**
     * Returns the Users database user's username.
     * @return string The Users database user's username.
     */
    public function getUsersDBUserName(): string;

    /**
     * Returns the Users database user's password.
     * @return string The Users database user's password.
     */
    public function getUsersDBPassword(): string;

    /**
     * Returns the Passwords database user's username.
     * @return string The Passwords database user's username.
     */
    public function getPasswordsDBUserName(): string;

    /**
     * Returns the Passwords database user's password.
     * @return string The Passwords database user's password.
     */
    public function getPasswordsDBPassword(): string;

    /**
     * Returns the Privileges database user's username.
     * @return string The Privileges database user's username.
     */
    public function getPrivilegesDBUserName(): string;

    /**
     * Returns the Privileges database user's password.
     * @return string The Privileges database user's password.
     */
    public function getPrivilegesDBPassword(): string;

}
