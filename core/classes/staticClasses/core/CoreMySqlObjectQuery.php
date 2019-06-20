<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-16
 * Time: 16:38
 */

namespace DarlingCms\classes\staticClasses\core;

use DarlingCms\classes\database\SQL\MySqlObjectQuery;

/**
 * Class CoreMySqlObjectQueryCrud. This class serves as a static factory that
 * can be used to obtain MySqlObjectQuery implementation instances that can be
 * used to query specified databases. This class insures that only one
 * MySqlObjectQuery implementation instance is established for each database
 * connection.
 *
 * Note: The connections established by this class utilize the db
 * configurations defined by the CoreValues static class to determine
 * the parameters of each database connection.
 *
 * @todo This class was once name CoreMySqlQuery, it has been renamed to CoreMySqlObjectQuery so that it's name more accurately describes it's purpose, if a CoreMySqlQuery class is needed in the future it will have to be redefined, though here is a hint: it would be identical to this class expect that occurrences of MySqlObjectQuery would be replaced with MySqlQuery....
 *
 * @package DarlingCms\classes\staticClasses\core
 *
 * @see CoreValues
 * @see CoreMySqlObjectQuery
 * @see CoreMySqlObjectQuery::DbConnection()
 */
class CoreMySqlObjectQuery
{
    /**
     * @var array|MySqlObjectQuery[] Array of the MySqlObjectQuery implementation
     *                               instances used by this class.
     */
    private static $connections = array();

    /**
     * Returns a MySqlObjectQuery implementation instance that can be used to
     * query the specified database.
     *
     * @param string $databaseName The name of the database to establish a
     *                             connection to.
     *
     * @param string $charset (optional) The database's charset. Defaults to
     *                                   MySqlObjectQuery::DEFAULT_CHARSET.
     *
     * @return MySqlObjectQuery A MySqlObjectQuery instance for the specified database.
     */
    public static function DbConnection(string $databaseName, string $charset = MySqlObjectQuery::DEFAULT_CHARSET): MySqlObjectQuery
    {
        // Make sure a connection to the specified database has not already been initialized.
        if (isset(CoreMySqlObjectQuery::$connections[$databaseName]) === false) {
            // Create a MySqlObjectQuery instance for the specified database.
            CoreMySqlObjectQuery::$connections[$databaseName] = new MySqlObjectQuery(MySqlObjectQuery::getDsn(CoreValues::getDBHostName($databaseName), $databaseName, $charset), CoreValues::getDBUserName($databaseName), CoreValues::getDBPassword($databaseName));
        }
        // Return the MySqlObjectQuery instance for the specified database.
        return CoreMySqlObjectQuery::$connections[$databaseName];
    }
}
