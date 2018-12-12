<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-11
 * Time: 20:46
 */

namespace DarlingCms\interfaces\database\SQL;


use DarlingCms\interfaces\database\IPDO;

/**
 * Interface ISQLQuery. Defines an extension of the IPDO interface that defines
 * the contract of an class that can query a database that uses SQL as it's
 * query language.
 *
 * Note: Only classes that are specifically for use with a SQL database should
 * implement this interface.
 *
 * @package DarlingCms\interfaces\database\SQL
 */
interface ISQLQuery extends IPDO
{
    /**
     * Query the database.
     * @param string $sql The SQL statement to run.
     * @param array $params (optional) Any parameters that should be included in the query.
     * @return \PDOStatement A PDOStatement object representing the query's prepared statement.
     */
    public function runQuery(string $sql, array $params = array()): \PDOStatement;

    /**
     * Gets an instance of a specified class using data from the specified table to
     * construct the instance.
     * @param string $className The name of the class to return an instance of.
     * @param string $tableName The name of the table whose data will be used to construct the instance.
     * @return mixed An instance of the specified class constructed from the data in the specified table.
     */
    public function getObject(string $className, string $tableName);

    /**
     * Generate a DSN string based on specified parameters.
     * @param string $host The host.
     * @param string $dbName The database name.
     * @param string $charset (optional) The charset. Defaults to an empty string.
     * @return string
     */
    public static function getDsn(string $host, string $dbName, string $charset = ''): string;

}
