<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-21
 * Time: 13:10
 */

namespace DarlingCms\abstractions\crud;


use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;

/**
 * Class AMySqlObjectQueryCrud. Defines an abstract implementation of the ISqlQueryCrud,
 * and ISqlObjectQueryCrud interfaces that is intended to be used as a base class for
 * CRUD classes that use a MySqlObjectQuery implementation instance to perform CRUD
 * operations on a specific table in a MySql database.
 *
 * Note: This class does not actually define any CRUD methods in order to
 *       allow implementations complete control over how such methods may be
 *       implemented if implemented at all. The purpose of the class is simply
 *       to provide base methods that are likely to be used by implementations
 *       of this class.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see
 * @see ISqlQueryCrud
 * @see ISqlObjectQueryCrud
 * @see AMySqlQueryCrud
 * @see AMySqlObjectQueryCrud
 * @see AMySqlObjectQueryCrud::MOD_TYPE_CREATE
 * @see AMySqlObjectQueryCrud::MOD_TYPE_READ
 * @see AMySqlObjectQueryCrud::MOD_TYPE_UPDATE
 * @see AMySqlObjectQueryCrud::MOD_TYPE_DELETE
 * @see AMySqlObjectQueryCrud::__construct()
 * @see AMySqlObjectQueryCrud::tableExists()
 * @see AMySqlObjectQueryCrud::hasResults()
 * @see AMySqlObjectQueryCrud::formatClassName()
 * @see AMySqlObjectQueryCrud::generateTable()
 * @see MySqlObjectQuery
 */
abstract class AMySqlObjectQueryCrud extends AMySqlQueryCrud implements ISqlQueryCrud, ISqlObjectQueryCrud
{

    /**
     * AMySqlObjectQueryCrud constructor. Injects the MySqlObjectQuery instance used for CRUD
     * operations. Set's the name of the table CRUD operations will be performed on.
     *
     * @param MySqlObjectQuery $mySqlObjectQuery The MySqlObjectQuery instance that will handle CRUD
     *                               operations.
     *
     * @param string $tableName The name of the table CRUD operations will be
     *                          performed on.
     */
    public function __construct(MySqlObjectQuery $mySqlObjectQuery, string $tableName)
    {
        parent::__construct($mySqlObjectQuery, $tableName);
    }

    /**
     * Formats the results of get_class().
     *
     * @param string $className The string returned by the get_class() function.
     *
     * @return string The formatted class name. This will include the fully qualified
     *                namespace.
     *
     *                For example:
     *
     *                formatClassName('Some\Namespace\SomeClass');
     *
     *                The call above would return '\\Some\\Namespace\\SomeClass'
     *
     */
    public function formatClassName(string $className): string
    {
        return '\\' . $className;
    }

}
