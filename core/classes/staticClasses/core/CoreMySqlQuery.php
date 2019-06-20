<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-16
 * Time: 16:38
 */

namespace DarlingCms\classes\staticClasses\core;

use DarlingCms\classes\database\SQL\MySqlQuery;

/**
 * Class CoreMySqlQueryCrud. This class serves as a static factory that can be used
 * to obtain MySqlQuery implementation instances that can be used to query specified
 * databases. This class insures that only one MySqlQuery implementation instance is
 * established for each database connection.
 *
 * Note: The connections established by this class utilize the db configurations defined
 * by the CoreValues static class to determine each database connection's parameters.
 *
 * Note: Only databases whose parameters are defined in the site's configuration file will
 *       be considered valid.
 *       i,e. only database's whose connection parameters can be determined by the following
 *       CoreValues methods are supported:
 *       - CoreValues::getDBHostName() // Note: If CoreValues::getDBHostName($databaseName)
 *                                              returns an empty string then the database
 *                                              is not supported.
 *       - CoreValues::getDBUserName() // Note: If CoreValues::getDBUserName($databaseName)
 *                                              returns an empty string then the database
 *                                              is not supported.
 *       - CoreValues::getDBPassword() // Note: If CoreValues::getDBPassword($databaseName)
 *                                              returns an empty string then the database
 *                                              is not supported.
 *
 *
 * @package DarlingCms\classes\staticClasses\core
 *
 * @see CoreValues
 * @see CoreValues::getDBHostName()
 * @see CoreValues::getDBUserName()
 * @see CoreValues::getDBPassword()
 *
 * @see CoreMySqlQuery::DbConnection()
 */
class CoreMySqlQuery
{
    /**
     * @var array|MySqlQuery[] Array of MySqlQuery implementation instances used
     *                         by this instance.
     */
    private static $connections = array();

    /**
     * Returns a MySqlQuery implementation instance that can be used to query the
     * specified database.
     *
     * @param string $databaseName The name of the database to establish a
     *                             connection to.
     *                             Note: Only databases whose parameters are
     *                                   defined in the site's configuration
     *                                   file will be considered valid.
     *
     * @param string $charset (optional) The database's charset.
     *                                   Defaults to MySqlQuery::DEFAULT_CHARSET.
     *
     * @return MySqlQuery A MySqlQuery implementation instance for the specified database.
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
