<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-17
 * Time: 02:06
 */

namespace DarlingCms\classes\factory;

use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\staticClasses\core\CoreMySqlObjectQuery;
use DarlingCms\classes\crud\MySqlActionCrud;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\classes\crud\MySqlUserCrud;
use DarlingCms\classes\crud\MySqlUserPasswordCrud;
use DarlingCms\interfaces\factory\ICoreMySqlCrudFactory;

/**
 * Class CoreMySqlCrudFactory. This class can be used as a factory to create instances
 * of one of the following Core MySql*Crud objects:
 *
 * - MySqlActionCrud
 *
 * - MySqlPermissionCrud
 *
 * - MySqlRoleCrud
 *
 * - MySqlUserCrud
 *
 * - MySqlUserPasswordCrud
 *
 * @package DarlingCms\classes\factory
 *
 * @see MySqlActionCrud
 * @see MySqlPermissionCrud
 * @see MySqlRoleCrud
 * @see MySqlUserCrud
 * @see MySqlUserPasswordCrud
 */
class CoreMySqlCrudFactory implements ICoreMySqlCrudFactory
{
    /**
     * Returns an instance of a MySqlActionCrud object.
     *
     * @return MySqlActionCrud An instance of a MySqlActionCrud object.
     */
    public function getActionCrud(): MySqlActionCrud
    {
        return new MySqlActionCrud(CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName()));
    }

    /**
     * Returns an instance of a MySqlPermissionCrud object.
     *
     * @return MySqlPermissionCrud An instance of a MySqlPermissionCrud object.
     */
    public function getPermissionCrud(): MySqlPermissionCrud
    {
        return new MySqlPermissionCrud(CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName()), $this->getActionCrud());
    }

    /**
     * Returns an instance of a MySqlRoleCrud object.
     *
     * @return MySqlRoleCrud An instance of a MySqlRoleCrud object.
     */
    public function getRoleCrud(): MySqlRoleCrud
    {
        return new MySqlRoleCrud(CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName()), $this->getPermissionCrud());
    }

    /**
     * Returns an instance of a MySqlUserCrud object.
     *
     * @return MySqlUserCrud An instance of a MySqlUserCrud object.
     */
    public function getUserCrud(): MySqlUserCrud
    {
        return new MySqlUserCrud(CoreMySqlObjectQuery::DbConnection(CoreValues::getUsersDBName()), $this->getRoleCrud());
    }

    /**
     * Returns an instance of a MySqlUserPasswordCrud object.
     *
     * @return MySqlUserPasswordCrud An instance of a MySqlUserPasswordCrud object.
     */
    public function getPasswordCrud(): MySqlUserPasswordCrud
    {
        return new MySqlUserPasswordCrud(CoreMySqlObjectQuery::DbConnection(CoreValues::getPasswordsDBName()));
    }

}
