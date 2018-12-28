<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 22:05
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\privilege\Action;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\classes\privilege\Role;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\privilege\IRole;

/**
 * Class MySqlRoleCrud. Defines an implementation of the IUserCrud interface
 * that extends the AMySqlRoleCrud abstract class. This implementation can be used
 * to perform CRUD operations on role data in a MySql database. */
class MySqlRoleCrud extends AMySqlQueryCrud implements IRoleCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ROLES_TABLE_NAME = 'roles';

    private $permissionCrud;

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations on role data.
     * Sets the PermissionCrud instance used for CRUD operations on permission data.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     */
    public function __construct(MySqlQuery $MySqlQuery, IPermissionCrud $permissionCrud)
    {
        parent::__construct($MySqlQuery, self::ROLES_TABLE_NAME);
        $this->permissionCrud = $permissionCrud;
    }

    /**
     * Create a new role.
     * @param IRole $role The IRole implementation instance that represents the role.
     * @return bool True if role was created, false otherwise.
     */
    public function create(IRole $role): bool
    {
        // make sure a role with same name does not already exist.
        if ($this->roleExists($role->getRoleName()) === true) {
            return false;
        }
        // create user
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (roleName, rolePermissions, IRoleType) VALUES (?,?,?)',
            [
                $role->getRoleName(),
                $this->packPermissions($role),
                $this->formatClassName(get_class($role))
            ]
        );
        return $this->roleExists($role->getRoleName());
    }

    /**
     * Read the specified role's data from the database and return an appropriate IRole implementation instance.
     * @param string $roleName The name of the role to read.
     * @return IRole An appropriate IRole implementation instance.
     */
    public function read(string $roleName): IRole
    {
        if ($this->roleExists($roleName) === true) {
            // 1. get role data
            $roleData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE roleName=? LIMIT 1', [$roleName])->fetchAll(\PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($roleData['roleName'], $this->unpackPermissions($roleData['rolePermissions']));
            // 3. Instantiate the appropriate IRole implementation based on the role data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE roleName=? LIMIT 1', $this->getClassName($roleName), [$roleName], $ctor_args);
            return array_shift($results);
        }
        return new Role('Anonymous', [new Permission('No Access', new Action('Inaction', 'No access!'))]); // dev value
    }

    /**
     * Update the specified role.
     * @param string $roleName The name of the role to update.
     * @param IRole $newRole The IRole implementation that represents the new role.
     * @return bool True if role was updated, false otherwise.
     */
    public function update(string $roleName, IRole $newRole): bool
    {
        if ($this->delete($roleName) === true) {
            if ($this->create($newRole) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Delete a specified role.
     * @param string $roleName The name of the role to delete.
     * @return bool True if role was deleted, false otherwise.
     */
    public function delete(string $roleName): bool
    {
        $this->MySqlQuery->executeQuery('DELETE FROM roles WHERE roleName=? LIMIT 1', [$roleName]);
        return $this->roleExists($roleName) === false;
    }

    /**
     * Creates the roles table.
     * @return bool True if the roles table was created, false otherwise.
     */
    protected function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            roleName VARCHAR(256) NOT NULL UNIQUE,
            rolePermissions JSON NOT NULL,
            IRoleType VARCHAR(256) NOT NULL
        );') === false) {
            error_log('User Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    /**
     * Creates an array of IPermission implementation names based on the IPermission implementations
     * assigned to the role, and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         'PermissionName1',
     *         'PermissionName2',
     *         'PermissionName3',
     *     ]
     * }
     * @param IRole $role The IRole implementation whose roles are to be packed.
     * @return string The packed permission names, i.e., the json string representing the data.
     */
    final private function packPermissions(IRole $role): string
    {
        $permissions = array();
        foreach ($role->getPermissions() as $permission) {
            array_push($permissions, $permission->getPermissionName());
        }
        return json_encode($permissions);
    }

    /**
     * Unpack the role's permissions.
     * @param string $packedPermissions The packed permissions, i.e., the json string representing the data.
     * @return array An array of the IPermission implementation instances assigned to the role.
     */
    final private function unpackPermissions(string $packedPermissions): array
    {
        $unpackedData = json_decode($packedPermissions);
        $permissions = [];
        foreach ($unpackedData as $permissionName) {
            array_push($permissions, $this->permissionCrud->read($permissionName));
        }
        return $permissions;
    }

    /**
     * Determine whether or not a specified role exists.
     * @param string $roleName The name of the role to check for.
     * @return bool True if role exists, false otherwise.
     */
    private function roleExists(string $roleName): bool
    {
        $roleData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE roleName=?', [$roleName])->fetchAll();
        if (empty($roleData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Get the fully qualified namespaced classname of the specified role.
     * @param string $roleName The name of the role.
     * @return string The fully qualified namespaced classname of the specified role.
     */
    private function getClassName(string $roleName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IRoleType FROM roles WHERE roleName=? LIMIT 1', [$roleName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IRoleType'];
    }
}
