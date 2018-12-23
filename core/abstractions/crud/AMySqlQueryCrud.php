<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 13:10
 */

namespace DarlingCms\abstractions\crud;


use DarlingCms\classes\database\SQL\MySqlQuery;

/**
 * Class AMySqlQueryCrud. Defines an abstract class that is intended to be used
 * as a base class for CRUD classes that use a MySqlQuery object to perform
 * CRUD operations on a specific table in a MySql database.
 * @package DarlingCms\abstractions\crud
 * @see MySqlQuery
 */
abstract class AMySqlQueryCrud
{
    protected $MySqlQuery;
    protected $tableName;

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations. Set's the
     * name of the table CRUD operations will be performed on.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     * @param string $tableName The name of the table CRUD operations will be performed on.
     */
    public function __construct(MySqlQuery $MySqlQuery, string $tableName)
    {
        $this->MySqlQuery = $MySqlQuery;
        $this->tableName = $tableName;
        if ($this->tableExists($this->tableName) === false) {
            if ($this->generateTable() === false) {
                error_log('MySqlQueryCrud implementation error: Unable to create table' . $this->tableName);
            }
        }
    }

    /**
     * Returns true if the specified table exists in the current database, false otherwise.
     * @param string $tableName The name of the table to check for.
     * @return bool true if the specified table exists in the current database, false otherwise.
     * @see https://www.quora.com/How-do-you-check-if-your-table-exists-or-not-in-MySQL
     * @see https://stackoverflow.com/questions/167576/check-if-table-exists-in-sql-server
     */
    protected function tableExists(string $tableName)
    {
        $results = $this->MySqlQuery->executeQuery('SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$tableName]);
        return $this->hasResults($results);
    }

    /**
     * Check if there are any results associated with the specified PDOStatement instance.
     * @param \PDOStatement $statement The PDOStatement to check.
     * @return bool True if there are any results, false otherwise.
     */
    protected function hasResults(\PDOStatement $statement): bool
    {
        $count = 0;
        foreach ($statement as $result) {
            $count++;
        }
        return $count > 0;
    }

    /**
     * Formats the results of get_class().
     * @param string $className The string returned by the get_class() function.
     * @return string The formatted class name. This will include the fully qualified namespace.
     * For example:
     * formatClassName('Some\Namespace\SomeClass') would return '\\Some\\Namespace\\SomeClass'
     */
    final protected function formatClassName(string $className): string
    {
        return '\\' . $className;
    }

    /**
     * Creates a table named using the value of the $tableName property.
     * Note: This method is intended to be calle by the __construct() method on instantiation.
     * NOTE: Implementations MUST implement this method in order to insure
     * the __construct() method can create the table used by the
     * implementation if it does not already exist.
     * @return bool True if table was created, false otherwise.
     */
    abstract protected function generateTable(): bool;
}
