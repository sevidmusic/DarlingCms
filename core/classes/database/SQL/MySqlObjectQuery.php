<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-11
 * Time: 21:44
 */

namespace DarlingCms\classes\database\SQL;

use DarlingCms\interfaces\database\IPDO;
use DarlingCms\interfaces\database\SQL\ISqlObjectQuery;
use DarlingCms\interfaces\database\SQL\ISqlQuery;
use \PDO;

/**
 * Class MySqlObjectQuery. Defines an implementation of the ISqlObjectQuery interface that
 * extends the MySqlQuery class. This class can be used to run queries against a
 * MySql database, and more specifically, can be used to build objects of a specified
 * type from the data queried from the database.
 *
 * @package DarlingCms\classes\database\SQL
 *
 * @see PDO
 * @see IPDO
 * @see ISqlQuery
 * @see ISqlObjectQuery
 * @see MySqlQuery
 * @see MySqlQuery::DEFAULT_OPTIONS
 * @see MySqlQuery::DEFAULT_CHARSET
 * @see MySqlQuery::__construct()
 * @see MySqlQuery::executeQuery()
 * @see MySqlQuery::getDsn()
 * @see MySqlObjectQuery
 * @see MySqlObjectQuery::getClass()
 */
class MySqlObjectQuery extends MySqlQuery implements ISqlQuery, ISqlObjectQuery
{
    /**
     * Gets an instance of a specified class using data returned by the specified SQL
     * query to construct the instance.
     *
     * @param string $sql The SQL query to run.
     *
     * @param string $className The name of the class to return an instance of.
     *
     * @param array $params Array of query parameters.
     *
     * @param array $ctor_args Array of parameters to pass to the class being
     *                         instantiated's __construct() method.
     *
     * @return mixed|object An instance of the specified class constructed from
     *                      the data in the specified table.
     *
     * <br>Hint: This method determines which fetch style to use based on whether
     *           or not the $ctor_args array is empty. If $ctor_args is empty,
     *           this method will use the PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE
     *           fetch style options to give the specified class's __set() method
     *           an opportunity to govern the construction of the object instance.
     *
     * This is best if the class being instantiated from the query results implements
     * a NON EMPTY __set() method.
     *
     * This approach gives preference to the __set() method as the __set() method will be
     * called after the __construct() method has been called, and the values have been
     * set by PDO.
     *
     * Logic:
     *  1. Appropriate class instance's __construct() called
     *  2. PDO sets values using results from querying the database
     *  3. The instance's __set() method is called(), and it is expected
     *     that the instance's __set() method will take responsibility for
     *     properly constructing the object from the results of the query.
     *
     * If $ctor_args is not empty, this method will use the PDO::FETCH_CLASS option alone.
     * This is best if the class being instantiated does not implement a __set() method.
     * This approach gives preference to the __construct() method as the __construct()
     * method will be called after property values have been set.
     * Logic:
     *  1. PDO sets values
     *  2. Appropriate class instance's __construct() method is called. In this context
     *     it is expected that the instance's __construct() method will take responsibility
     *     for properly constructing the object from the results of the query.
     * <br>
     * WARNING:
     * <br>
     * The documentation on php.net is vague about much of this if it is documented at all.
     * A lot of tinkering was done to see the consequences of different fetch style option
     * combinations. Please note that these are just dev notes based on the observations
     * of the effects of different fetch style option combinations, they do not serve as
     * a sound documentation of the behavior of PDO, the fetchAll() method, how values are
     * set from a query when using FETCH_CLASS, or the order of method calls when
     * instantiating an object from a fetchAll(PDO::FETCH_CLASS) query.
     *
     * Hint: An empty __set() method can be implemented by classes that wish to prevent
     *       PDO implementation instances from setting undeclared property values when
     *       they are built from the results of a PDO query.
     *
     *       For example:
     *       class SomeClass {
     *          public function __set() { // Leave empty... }
     *       }
     *
     * The empty __set() method defined in the example above will prevent
     * PDO from setting undefined properties from query results.
     * For instance, the SomeClass example above has no properties,
     * if the PDO query returned any results in this context, the query
     * results that could not be matched to an appropriately named property
     * would be automatically defined for the object instance, this is not
     * okay as it can corrupt the design of an object by polluting it with
     * property values that were never intended to be defined by the object.
     *
     * For Example:
     * A PDO query that returned results for a table with two fields, Foo and Bar,
     * that tries to construct a SomeClass that does not implement an empty __set()
     * method would result in a SomeClass instance that had SomeClass->Foo and
     * SomeClass->Bar properties, this is obviously a corruption of the original
     * object, implementing an empty __set() method will prevent this.
     *
     *
     */
    public function getClass(string $sql, string $className, array $params = array(), array $ctor_args = array())
    {
        $fetchStyle = (empty($ctor_args) ? PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE : PDO::FETCH_CLASS);
        return $this->executeQuery($sql, $params)->fetchAll($fetchStyle, $className, $ctor_args);
    }

}
