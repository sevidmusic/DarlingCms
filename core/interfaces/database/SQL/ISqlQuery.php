<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-11
 * Time: 20:46
 */

namespace DarlingCms\interfaces\database\SQL;

use DarlingCms\interfaces\database\IPDO;
use PDOStatement;

/**
 * Interface ISQLQuery. Defines an extension of the IPDO interface that defines
 * the contract of an class that can query a database that uses SQL as it's
 * query language.
 *
 * Note: Only classes that are specifically designed to query a SQL database should
 *       implement this interface.
 *
 * @package DarlingCms\interfaces\database\SQL
 *
 * @see ISqlQuery::executeQuery()
 * @see ISqlQuery::getDsn()
 */
interface ISqlQuery extends IPDO
{
    /**
     * Executes an SQL query.
     *
     * @param string $sql The SQL statement to execute.
     *
     * @param array $params (optional) Any parameters that should be included in the query.
     *
     * @return PDOStatement A PDOStatement object representing the query's prepared statement.
     */
    public function executeQuery(string $sql, array $params = array()): PDOStatement;

    /**
     * Generate a DSN string based on specified parameters.
     *
     * @param string $host The host.
     *
     * @param string $dbName The database name.
     *
     * @param string $charset (optional) The charset. Defaults to an empty string.
     *
     * @return string The DSN string that was generated based on the specified parameters.
     */
    public static function getDsn(string $host, string $dbName, string $charset = ''): string;

}
