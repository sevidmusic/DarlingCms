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
use DarlingCms\classes\privilege\Role;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\privilege\IRole;

class MySqlRoleCrud extends AMySqlQueryCrud implements IRoleCrud
{
    const ROLES_TABLE_NAME = 'roles';

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations. Set's the
     * name of the table CRUD operations will be performed on.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     * @param string $tableName The name of the table CRUD operations will be performed on.
     */
    public function __construct(MySqlQuery $MySqlQuery)
    {
        parent::__construct($MySqlQuery, self::ROLES_TABLE_NAME);
    }

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

    public function read(string $roleName): IRole
    {
        if ($this->roleExists($roleName) === true) {
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE roleName=?', $this->getClassName($roleName), [$roleName]);
            return array_shift($results);
        }
        return new Role('Anonymous'); // dev value
    }

    public function update(string $roleName, IRole $newRole): bool
    {
        // TODO: Implement update() method.
        return false;
    }

    public function delete(string $roleName): bool
    {
        // TODO: Implement delete() method.
        return false;
    }

    /**
     * Creates a table named using the value of the $tableName property.
     * Note: This method is intended to be calle by the __construct() method on instantiation.
     * NOTE: Implementations MUST implement this method in order to insure
     * the __construct() method can create the table used by the
     * implementation if it does not already exist.
     * @return bool True if table was created, false otherwise.
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
     * Creates an array of IPermission implementation class names based on the IPermission implementations
     * assigned to the role, and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         '\\Some\\IRole\\Implementation\\Permission1',
     *         '\\Some\\IRole\\Implementation\\Permission2',
     *         '\\Some\\IRole\\Implementation\\Permission3',
     *     ]
     * }
     * @param IRole $role
     * @return string
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
     * @param string $userName
     * @return bool
     * // implemented : )
     */
    private function roleExists(string $roleName): bool
    {
        $roleData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE roleName=?', [$roleName])->fetchAll();
        if (empty($roleData) === true) {
            return false;
        }
        return true;
    }

    private function getClassName(string $roleName): string
    {
        // Implement Steps //
        // 1. Check if role exists
        // 2. executeQuery()->fetchAll() to get roles's IRoleType
        // 3. return user's IUserType
        return '\\DarlingCms\\classes\\privilege\\Role'; // dev value...
    }
}
