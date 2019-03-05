<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-04
 * Time: 17:03
 */

namespace DarlingCms\classes\config;


use DarlingCms\interfaces\config\IDBConfig;

/**
 * Class DBConfig. Defines an implementation of the IDBConfig interface that can be used
 * to define a database configuration that includes the following:
 * - The database name
 * - The database host
 * - The database user's username
 * - The database user's password
 * @package DarlingCms\classes\config
 * @see DBConfig::getDBName()
 * @see DBConfig::getDBHostName()
 * @see DBConfig::getDBUsername()
 * @see DBConfig::getDBUserPassword() *
 */
class DBConfig implements IDBConfig
{
    /**
     * @var string $dbName The name of the database.
     */
    private $dbName;

    /**
     * @var string $dbHost The database host.
     */
    private $dbHost;

    /**
     * @var string $dbUsername The database user's username.
     */
    private $dbUsername;

    /**
     * @var string $dbUserPassword The database user's password.
     */
    private $dbUserPassword;

    /**
     * DBConfig constructor. Set's the database configuration.
     * @param string $dbName The name of the database.
     * @param string $dbHost The database host.
     * @param string $dbUsername The database user's username.
     * @param string $dbUserPassword The database user's password.
     */
    public function __construct(string $dbName, string $dbHost, string $dbUsername, string $dbUserPassword)
    {
        $this->dbName = $dbName;
        $this->dbHost = $dbHost;
        $this->dbUsername = $dbUsername;
        $this->dbUserPassword = $dbUserPassword;
    }

    /**
     * @return string The name of the database.
     */
    public function getDBName(): string
    {
        return $this->getDBName();
    }

    /**
     * @return string The database host.
     */
    public function getDBHostName(): string
    {
        return $this->getDBHostName();
    }

    /**
     * @return string The database user's username.
     */
    public function getDBUsername(): string
    {
        return $this->getDBUsername();
    }

    /**
     * @return string The database user's password.
     */
    public function getDBUserPassword(): string
    {
        return $this->getDBUserPassword();
    }

}
