<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-21
 * Time: 22:05
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlRoleCrud;
use DarlingCms\classes\privilege\Action;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\classes\privilege\Role;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\privilege\IRole;
use PDO;

/**
 * Class MySqlRoleCrud. Defines an implementation of the IUserCrud interface
 * that extends the AMySqlRoleCrud abstract class. This implementation can be used
 * to perform CRUD operations on role data in a MySql database. */
class MySqlRoleCrud extends AMySqlRoleCrud implements IRoleCrud
{
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
     * Note: This method will return an Anonymous Role with no Permissions if the specified Role cannot be read.
     * @param string $roleName The name of the role to read.
     * @return IRole An appropriate IRole implementation instance, or an IRole implementation instance that
     *               represents an Anonymous Role with no Permissions if the specified Role cannot be read.
     */
    public function read(string $roleName): IRole
    {
        if ($this->roleExists($roleName) === true) {
            // 1. get role data
            $roleData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE roleName=? LIMIT 1', [$roleName])->fetchAll(PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($roleData['roleName'], $this->unpackPermissions($roleData['rolePermissions']));
            // 3. Instantiate the appropriate IRole implementation based on the role data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE roleName=? LIMIT 1', $this->getClassName($roleName), [$roleName], $ctor_args);
            return array_shift($results);
        }
        return new Role('Anonymous', [new Permission('No Access', [new Action('Inaction', 'Do nothing.')])]);
    }

    /**
     * Read all stored roles into an array.
     * @return array|IRole[] An array of all stored roles.
     */
    public function readAll(): array
    {
        $roleNames = $this->MySqlQuery->executeQuery('SELECT roleName FROM roles')->fetchAll(PDO::FETCH_ASSOC);
        sort($roleNames, SORT_ASC);
        $roles = array();
        foreach ($roleNames as $roleName) {
            array_push($roles, $this->read($roleName['roleName']));
        }
        return $roles;//$this->MySqlQuery->executeQuery('SELECT * FROM roles')->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update the specified role.
     * @param string $roleName The name of the role to update.
     * @param IRole $newRole The IRole implementation that represents the new role.
     * @return bool True if role was updated, false otherwise.
     */
    public function update(string $roleName, IRole $newRole): bool
    {
        if ($this->roleExists($roleName) === true) {
            $this->modType = self::MOD_TYPE_UPDATE;
            $this->originalRole = $this->read($roleName);
            $this->modifiedRole = $newRole;
            if ($this->delete($roleName) === true && $this->create($newRole) === true) {
                $this->notify();
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
        if ($this->roleExists($roleName) === true) {
            if ($this->modType !== self::MOD_TYPE_UPDATE) {
                $this->modType = self::MOD_TYPE_DELETE;
                $this->originalRole = $this->read($roleName);
                $this->modifiedRole = $this->originalRole;
            }
            $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE roleName=? LIMIT 1', [$roleName]);
            if ($this->roleExists($roleName) === false) {
                if ($this->modType === self::MOD_TYPE_DELETE) {
                    $this->notify();
                }
                return true;
            }
        }
        return false;
    }

}
