<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-21
 * Time: 13:10
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use PDOStatement;

/**
 * Class AMySqlQueryCrud. Defines an abstract implementation of the ISqlQueryCrud
 * interface that can be used as a base class for CRUD classes that use a MySqlQuery
 * object to perform CRUD operations on a specific table in a MySql database.
 *
 * Note: This class does not actually define any CRUD methods in order to
 *       allow implementations complete control over how such methods may
 *       be implemented if implemented at all. Instead, it defines base
 *       methods that can be used by child implementations to perform
 *       common logic.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see ISqlQueryCrud
 * @see MySqlQuery
 * @see AMySqlQueryCrud::MOD_TYPE_CREATE
 * @see AMySqlQueryCrud::MOD_TYPE_READ
 * @see AMySqlQueryCrud::MOD_TYPE_UPDATE
 * @see AMySqlQueryCrud::MOD_TYPE_DELETE
 * @see AMySqlQueryCrud::__construct()
 * @see AMySqlQueryCrud::tableExists()
 * @see AMySqlQueryCrud::hasResults()
 * @see AMySqlQueryCrud::generateTable()
 */
abstract class AMySqlQueryCrud implements ISqlQueryCrud
{
    /**
     * @var MySqlQuery The MySqlQuery implementation instance used to perform
     *                 CRUD operations on data in the database.
     */
    protected $MySqlQuery;

    /**
     * @var string The name of the table crud operations are to be performed on in the
     *             database.
     */
    protected $tableName;

    /**
     * @var int A numeric value representing the type of modification currently being
     *          performed. MUST equal one of the following class constants:
     *
     *              <br>- AMySqlQueryCrud::MOD_TYPE_CREATE
     *
     *              <br>- AMySqlQueryCrud::MOD_TYPE_READ
     *
     *              <br>- AMySqlQueryCrud::MOD_TYPE_UPDATE
     *
     *              <br>- AMySqlQueryCrud::MOD_TYPE_DELETE
     */
    public $modType; // @todo Should probably be protected...

    /**
     * @var string Value that can be used by implementations that need to identify
     *             or indicate that the type of query that is being performed will
     *             create new data.
     */
    const MOD_TYPE_CREATE = 2;

    /**
     * @var string Value that can be used by implementations that need to identify
     *             or indicate that the type of query that is being performed will
     *             read data.
     */
    const MOD_TYPE_READ = 4;

    /**
     * @var string Value that can be used by implementations that need to identify
     *             or indicate that the type of query that is being performed will
     *             update data.
     */
    const MOD_TYPE_UPDATE = 6;

    /**
     * @var string Value that can be used by implementations that need to identify
     *             or indicate that the type of query that is being performed will
     *             delete data.
     */
    const MOD_TYPE_DELETE = 8;


    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD
     * operations. Set's the name of the table CRUD operations will be performed on.
     *
     * @param MySqlQuery $mySqlQuery The MySqlQuery instance that will handle CRUD
     *                               operations.
     *
     * @param string $tableName The name of the table CRUD operations will be
     *                          performed on.
     */
    public function __construct(MySqlQuery $mySqlQuery, string $tableName)
    {
        $this->MySqlQuery = $mySqlQuery;
        $this->tableName = $tableName;
        if ($this->tableExists($this->tableName) === false) {
            if ($this->generateTable() === false) {
                error_log('MySqlQueryCrud implementation error: Unable to create table ' . $this->tableName);
            }
        }
    }

    /**
     * Returns true if the specified table exists in the current database, false otherwise.
     *
     * @param string $tableName The name of the table to check for.
     *
     * @return bool True if the specified table exists in the current database, false
     *              otherwise.
     *
     * @see https://www.quora.com/How-do-you-check-if-your-table-exists-or-not-in-MySQL
     *
     * @see https://stackoverflow.com/questions/167576/check-if-table-exists-in-sql-server
     */
    public function tableExists(string $tableName): bool
    {
        $results = $this->MySqlQuery->executeQuery('SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$tableName]);
        return $this->hasResults($results);
    }

    /**
     * Check if there are any results associated with the specified PDOStatement instance.
     *
     * @param PDOStatement $statement The PDOStatement to check.
     *
     * @return bool True if there are any results, false otherwise.
     */
    public function hasResults(PDOStatement $statement): bool
    {
        $count = 0;
        foreach ($statement as $result) {
            $count++;
        }
        return $count > 0;
    }

    /**
     * Creates a table named using the value of the $tableName property.
     *
     * Note: This method is intended to be called by the __construct()
     *       method on instantiation.
     *
     * NOTE: Implementations MUST implement this method in order to insure
     * the __construct() method can create the table used by the implementation
     * if it does not already exist.
     *
     * @return bool True if table was created, false otherwise.
     */
    abstract public function generateTable(): bool;
}
