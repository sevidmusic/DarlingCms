<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 23:23
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\interfaces\privilege\IPermission;

/**
 * Class MySqlPermissionCrud. Defines an implementation of the IPermissionCrud interface
 * that extends the AMySqlPermissionCrud abstract class. This implementation can be used
 * to perform CRUD operations on Permission data in a MySql database.
 * @package DarlingCms\classes\crud
 */
class MySqlPermissionCrud extends AMySqlQueryCrud implements IPermissionCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const PERMISSIONS_TABLE_NAME = 'permissions';

    /**
     * @var IActionCrud Injected instance of an IActionCrud implementation.
     */
    private $actionCrud;

    /**
     * MySqlPermissionCrud constructor. Injects the MySqlQuery instance used for CRUD operations on
     * permission data. Injects the IActionCrud implementation instance used for CRUD operations on
     * action data.
     * @param MySqlQuery $mySqlQuery
     * @param IActionCrud $actionCrud
     */
    public function __construct(MySqlQuery $mySqlQuery, IActionCrud $actionCrud)
    {
        parent::__construct($mySqlQuery, self::PERMISSIONS_TABLE_NAME);
        $this->actionCrud = $actionCrud;
    }

    /**
     * Creates the permissions table.
     * @return bool True if permissions table was created, false otherwise.
     */
    protected function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            permissionName VARCHAR(242) NOT NULL UNIQUE,
            permissionActions TEXT NOT NULL,
            IPermissionType VARCHAR(242) NOT NULL
        );') === false) {
            error_log('Permission Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

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
        if ($this->delete($permissionName) === true) {
            if ($this->create($newPermission) === true) {
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
        $this->MySqlQuery->executeQuery('DELETE FROM permissions WHERE permissionName=? LIMIT 1', [$permissionName]);
        return $this->permissionExists($permissionName) === false;
    }

    /**
     * Determine whether or not a specified permission exists.
     * @param string $permissionName The name of the permission to check for.
     * @return bool True if permission exists, false otherwise.
     */
    private function permissionExists(string $permissionName): bool
    {
        $permissionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE permissionName=?', [$permissionName])->fetchAll();
        if (empty($permissionData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Get the fully qualified namespaced classname of the specified permission.
     * @param string $permissionName The name of the permission.
     * @return string The fully qualified namespaced classname of the specified permission.
     */
    private function getClassName(string $permissionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IPermissionType FROM permissions WHERE permissionName=? LIMIT 1', [$permissionName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IPermissionType'];
    }

    /**
     * Creates an array of IAction implementation names based on the IAction implementations
     * assigned to the IPermission implementation instance, and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         'ActionName1',
     *         'ActionName2',
     *         'ActionName3',
     *     ]
     * }
     * @param IPermission $Permission The IPermission implementation instance whose Permissions are to be packed.
     * @return string The packed permission names, i.e., the json string representing the data.
     */
    final private function packActions(IPermission $Permission): string
    {
        $permissions = array();
        foreach ($Permission->getActions() as $action) {
            array_push($permissions, $action->getActionName());
        }
        return json_encode($permissions);
    }

    /**
     * Unpack the Permission's permissions.
     * @param string $packedPermissions The packed permissions, i.e., the json string representing the data.
     * @return array An array of the IPermission implementation instances assigned to the Permission.
     */
    final private function unpackActions(string $packedPermissions): array
    {
        $unpackedData = json_decode($packedPermissions);
        $permissions = [];
        foreach ($unpackedData as $actionName) {
            array_push($permissions, $this->actionCrud->read($actionName));
        }
        return $permissions;
    }

}
