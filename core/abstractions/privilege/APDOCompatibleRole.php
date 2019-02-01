<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-25
 * Time: 04:34
 */

namespace DarlingCms\abstractions\privilege;


use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\interfaces\privilege\IPermission;
use DarlingCms\interfaces\privilege\IRole;

/**
 * Class APDOCompatibleRole. Defines an abstract implementation of the IRole interface
 * that is designed to play nice with PDO and the PDOStatement class's fetchAll() method.
 * Implementations of this abstract class can be instantiated from a fetchAll() call that
 * is structured as follows:
 *
 * $PDOStatement->fetchAll(PDO::FETCH_CLASS, $className, $ctor_args);
 *
 * IMPORTANT: ONLY USE THE FETCH_CLASS OPTION! DO NOT USE FETCH_PROPS_LATE OR PROPERTY VALUE SET BY
 * THE __construct() METHOD MAY BE OVERWRITTEN WHEN PDO SETS IT'S VALUES!!!
 *
 * The $ctor_args array should be structured to match the parameters expected by the __construct() method
 * defined by this abstract class:
 * $ctor_args = array(
 *                     $roleName, // the role's role name
 *                     $rolePermissions, // the array of IPermission implementation names assigned to the to the role.
 *                     $IRoleType, // The fully qualified namespace of the role's IRole implementation.
 *              )
 *
 * Note: The MySqlRoleCrud class's read() method does this very thing to instantiate IRole implementations
 * from role data stored in a MySql database.
 * @package DarlingCms\abstractions\privilege
 * @see MySqlRoleCrud::read()
 */
abstract class APDOCompatibleRole implements IRole
{
    protected $roleName;
    protected $permissions = array();

    /**
     * Role constructor.
     * @param string $roleName The name to assign to the Role.
     * @param array|IPermission[] ...$permissions The permissions to assign to the Role.
     */
    final public function __construct(string $roleName, array $permissions)
    {
        $this->roleName = $roleName;
        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }
    }

    final private function addPermission(IPermission $permission)
    {
        array_push($this->permissions, $permission);
    }

    /**
     * Returns the name of the role.
     * @return string The name of the role.
     */
    abstract public function getRoleName(): string;

    /**
     * Returns an array of the permissions assigned to the role.
     * @return array|IPermission[] Array of the permissions assigned to the role.
     */
    abstract public function getPermissions(): array;

    /**
     * Returns true if the role is assigned the specified permission, false otherwise.
     * @param IPermission $permission The permission to check for.
     * @return bool True if the role is assigned the specified permission, false otherwise.
     */
    abstract public function hasPermission(IPermission $permission): bool;

    /**
     * Implement empty set method since this object may be instantiated from a PDO query result, leaving
     * this method empty will prevent PDO from automatically setting undefined properties as public properties.
     */
    public function __set($name, $value)
    {
        /**
         * IMPORTANT! THIS METHOD MUST REMAIN EMPTY UNLESS YOU ARE COMPLETELY SURE YOU KNOW WHAT YOU ARE DOING
         * AND ARE WILLING TO ACCEPT THE CONSEQUENCES OF WHAT YOU ARE DOING!!! THE REASON THIS METHOD IS EMPTY
         * IS TO PREVENT ANY UNDECLARED PROPERTIES FROM BEING CREATED AND SET BY ACTORS OTHER THAN THIS CLASS.
         */
    }

}
