<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-21
 * Time: 23:23
 */

namespace DarlingCms\classes\crud;

use DarlingCms\abstractions\crud\AMySqlPermissionCrud;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\interfaces\privilege\IPermission;

/**
 * Class MySqlPermissionCrud. Defines an implementation of the IPermissionCrud interface
 * that extends the AMySqlPermissionCrud abstract class. This implementation can be used
 * to perform CRUD operations on Permission data in a MySql database.
 * @package DarlingCms\classes\crud
 */
class MySqlPermissionCrud extends AMySqlPermissionCrud implements IPermissionCrud
{

    /**
     * Create a new permission.
     * @param IPermission $permission The IPermission implementation instance that represents the permission.
     * @return bool True if permission was created, false otherwise.
     */
    public function create(IPermission $permission): bool
    {
        // make sure a permission with same name does not already exist.
        if ($this->permissionExists($permission->getPermissionName()) === true) {
            return false;
        }
        // create user
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (permissionName, permissionActions, IPermissionType) VALUES (?,?,?)',
            [
                $permission->getPermissionName(),
                $this->packActions($permission),
                $this->formatClassName(get_class($permission))
            ]
        );
        return $this->permissionExists($permission->getPermissionName());
    }

    /**
     * Read the specified permission's data from the database and return an appropriate IPermission implementation
     * instance.
     * @param string $permissionName The name of the permission to read.
     * @return IPermission An appropriate IPermission implementation instance.
     */
    public function read(string $permissionName): IPermission
    {
        if ($this->permissionExists($permissionName) === true) {
            // 1. get permission data
            $permissionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE permissionName=? LIMIT 1', [$permissionName])->fetchAll(\PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($permissionData['permissionName'], $this->unpackActions($permissionData['permissionActions']));
            // 3. Instantiate the appropriate IPermission implementation based on the permission data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE permissionName=? LIMIT 1', $this->getClassName($permissionName), [$permissionName], $ctor_args);
            return array_shift($results);
        }
        return new Permission('Anonymous', [new Permission('No Access', [])]); // dev value
    }

    /**
     * @return array|IPermission[]
     */
    public function readAll(): array
    {
        $permissionNames = $this->MySqlQuery->executeQuery('SELECT permissionName FROM ' . $this->tableName)->fetchAll(\PDO::FETCH_ASSOC);
        $permissions = array();
        foreach ($permissionNames as $permissionName) {
            array_push($permissions, $this->read($permissionName['permissionName']));
        }
        return $permissions;
    }

    /**
     * Update the specified permission.
     * @param string $permissionName The name of the permission to update.
     * @param IPermission $newPermission The IPermission implementation instance that represents
     *                                   the new permission.
     * @return bool True if permission was updated, false otherwise.
     */
    public function update(string $permissionName, IPermission $newPermission): bool
    {
        if ($this->permissionExists($permissionName) === true) {
            $this->modType = self::MOD_TYPE_UPDATE;
            $this->originalPermission = $this->read($permissionName);
            $this->modifiedPermission = $newPermission;
            if ($this->delete($permissionName) === true && $this->create($newPermission) === true) {
                $this->notify();
                return true;
            }
        }
        return false;
    }

    /**
     * Delete a specified permission.
     * @param string $permissionName The name of the permission to delete.
     * @return bool True if permission was deleted, false otherwise.
     */
    public function delete(string $permissionName): bool
    {
        if ($this->permissionExists($permissionName)) {
            if ($this->modType !== self::MOD_TYPE_UPDATE) {
                $this->modType = self::MOD_TYPE_DELETE;
                $this->originalPermission = $this->read($permissionName);
                $this->modifiedPermission = $this->originalPermission;
            }
            $this->MySqlQuery->executeQuery('DELETE FROM permissions WHERE permissionName=? LIMIT 1', [$permissionName]);
            if ($this->permissionExists($permissionName) === false) {
                $this->notify();
                return true;
            }
        }
        return false;
    }

}
