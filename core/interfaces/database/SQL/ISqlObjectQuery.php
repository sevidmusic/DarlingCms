<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-11
 * Time: 20:46
 */

namespace DarlingCms\interfaces\database\SQL;

use DarlingCms\interfaces\database\IPDO;

/**
 * Interface ISQLQuery. Defines an extension of the IPDO interface, and the
 * ISqlQuery interface, that defines the basic contract of class that can
 * query a database that uses SQL as it's query language, and can also
 * construct object instances from the data it queries from the database.
 *
 * Note: Only classes that are specifically designed to query a SQL database
 *       with the intention of constructing object instances from data in a
 *       database should implement this interface.
 *
 * @package DarlingCms\interfaces\database\SQL
 *
 * @see ISqlQuery::executeQuery()
 * @see ISqlQuery::getDsn()
 * @see ISqlObjectQuery::getClass()
 */
interface ISqlObjectQuery extends IPDO, ISqlQuery
{
    /**
     * Gets an instance of a specified class using data from the specified SQL query to
     * construct the instance.
     *
     * @param string $sql The SQL query to run.
     *
     * @param string $className The name of the class to return an instance of.
     *
     * @param array $params Array of parameters to pass to the query.
     *
     * @param array $ctor_args (Optional) Array of parameters to pass to the class's __construct() method.
     *                                    This is only used if the array is not empty.
     *
     * @return mixed An instance of the specified class constructed from the data in the specified table.
     *
     */
    public function getClass(string $sql, string $className, array $params = array(), array $ctor_args = array());

}
