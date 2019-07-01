<?php
/**
 * Created by Sevi Darling.
 */

namespace DarlingCms\interfaces\crud;

use PDOStatement;

/**
 * Interface ISqlQueryCrud. Defines the basic contract of an object that provides methods
 * that perform logic that is common to performing CRUD operations on data in a Sql
 * database.
 *
 * @package DarlingCms\interfaces\crud
 *
 * @see ISqlQueryCrud
 * @see ISqlQueryCrud::tableExists()
 * @see ISqlQueryCrud::hasResults()
 * @see ISqlQueryCrud::generateTable()
 */
interface ISqlQueryCrud
{
    /**
     * Returns true if the specified table exists in the current database, false otherwise.
     *
     * @param string $tableName The name of the table to check for.
     *
     * @return bool True if the specified table exists in the current database, false
     *              otherwise.
     *
     */
    public function tableExists(string $tableName): bool;

    /**
     * Check if there are any results associated with the specified PDOStatement instance.
     *
     * @param PDOStatement $statement The PDOStatement to check.
     *
     * @return bool True if there are any results, false otherwise.
     */
    public function hasResults(PDOStatement $statement): bool;

    /**
     * Creates the table this ISqlQueryCrud implementation instance performs
     * crud operation on if it does not already exist in the database.
     *
     * @return bool True if table was created, false otherwise.
     */
    public function generateTable(): bool;
}
