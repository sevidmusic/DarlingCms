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
}
