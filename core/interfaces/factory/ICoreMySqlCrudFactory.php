<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-17
 * Time: 01:51
 */

namespace DarlingCms\interfaces\factory;

use DarlingCms\classes\crud\MySqlActionCrud;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\classes\crud\MySqlUserCrud;
use DarlingCms\classes\crud\MySqlUserPasswordCrud;

/**
 * Interface ICoreMySqlCrudFactory. Defines the basic contract of an object that can be used as a
 * factory to create instances of one of the Core MySql*Crud objects.
 *
 * @package DarlingCms\interfaces\factory
 */
interface ICoreMySqlCrudFactory
{
    /**
     * Returns an instance of a MySqlActionCrud object.
     * @return MySqlActionCrud An instance of a MySqlActionCrud object.
     */
    public function getActionCrud(): MySqlActionCrud;

    /**
     * Returns an instance of a MySqlPermissionCrud object.
     * @return MySqlPermissionCrud An instance of a MySqlPermissionCrud object.
     */
    public function getPermissionCrud(): MySqlPermissionCrud;

    /**
     * Returns an instance of a MySqlRoleCrud object.
     * @return MySqlRoleCrud An instance of a MySqlRoleCrud object.
     */
    public function getRoleCrud(): MySqlRoleCrud;

    /**
     * Returns an instance of a MySqlUserCrud object.
     * @return MySqlUserCrud An instance of a MySqlUserCrud object.
     */
    public function getUserCrud(): MySqlUserCrud;

    /**
     * Returns an instance of a MySqlUserPasswordCrud object.
     * @return MySqlUserPasswordCrud An instance of a MySqlUserPasswordCrud object.
     */
    public function getPasswordCrud(): MySqlUserPasswordCrud;
}
