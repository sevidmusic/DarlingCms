<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 2019-03-04
 * Time: 16:55
 */

namespace DarlingCms\interfaces\config;

/**
 * Interface IDBConfig. Defines the basic contract of an object that represents a database configuration
 * that includes the following:
 * - The database name
 * - The database host
 * - The database user's username
 * - The database user's password
 * @package DarlingCms\interfaces\config
 * @see IDBConfig::getDBName()
 * @see IDBConfig::getDBHostName()
 * @see IDBConfig::getDBUsername()
 * @see IDBConfig::getDBUserPassword()
 */
interface IDBConfig
{
    public function getDBName();

    public function getDBHostName();

    public function getDBUsername();

    public function getDBUserPassword();
}
