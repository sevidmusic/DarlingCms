<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 23:23
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\interfaces\privilege\IPermission;

class MySqlPermissionCrud extends AMySqlQueryCrud implements IPermissionCrud
{
    /**
     * Creates a table named using the value of the $tableName property.
     * Note: This method is intended to be called by the __construct() method on instantiation.
     * NOTE: Implementations MUST implement this method in order to insure
     * the __construct() method can create the table used by the
     * implementation if it does not already exist.
     * @return bool True if table was created, false otherwise.
     */
    protected function generateTable(): bool
    {
        // TODO: Implement generateTable() method.
        return true; // true to prevent error until implemented
    }

    public function create(IPermission $permission): bool
    {
        // TODO: Implement create() method.
        return false;
    }

    public function read(string $permissionName): IPermission
    {
        // TODO: Implement read() method.
        return new Permission('Anonymous'); // dev value
    }

    public function update(string $permissionName, IPermission $newPermission): bool
    {
        // TODO: Implement update() method.
        return false;
    }

    public function delete(string $permissionName): bool
    {
        // TODO: Implement delete() method.
        return false;
    }

}
