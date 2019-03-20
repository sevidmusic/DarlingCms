<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-16
 * Time: 16:38
 */

namespace DarlingCms\classes\staticClasses\core;

use DarlingCms\classes\database\SQL\MySqlQuery;

/**
 * Class CoreMySqlQueryCrud. This class serves as a static factory that can be used to obtain MySqlQuery
 * implementation instances that can be used to query specified databases. This class insures that only
 * one MySqlQuery implementation instance is established for each database connection.
 *
 * Note: The connections established by this class utilize the db configuration defined by the CoreValues
 * static class to determine the db user, db password, and db host.
 * @package DarlingCms\classes\staticClasses\core
 * @see CoreValues
 */
class CoreMySqlQuery
{
    /**
     * @var array|MySqlQuery[]
     */
    private static $connections = array();

    /**
     * Returns a MySqlQuery implementation instance that can be used to query the specified database.
     * @param string $databaseName The name of the database to establish a connection to.
     * @param string $charset (optional) The database's charset. Defaults to MySqlQuery::DEFAULT_CHARSET.
     * @return MySqlQuery A MySqlQuery instance for the specified database.
     */
    public static function DbConnection(string $databaseName, string $charset = MySqlQuery::DEFAULT_CHARSET): MySqlQuery
    {
        // Make sure a connection to the specified database has not already been initialized.
        if (isset(CoreMySqlQuery::$connections[$databaseName]) === false) {
            // Create a MySqlQuery instance for the specified database.
            CoreMySqlQuery::$connections[$databaseName] = new MySqlQuery(MySqlQuery::getDsn(CoreValues::getDBHostName($databaseName), $databaseName, $charset), CoreValues::getDBUserName($databaseName), CoreValues::getDBPassword($databaseName));
        }
        // Return the MySqlQuery instance for the specified database.
        return CoreMySqlQuery::$connections[$databaseName];
    }
}
