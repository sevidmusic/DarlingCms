<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:37
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\privilege\IPermission;

/**
 * Interface IPermissionCrud. Defines the contract of an object that is responsible for
 * mapping IPermission implementation instances so they can be created in, read from,
 * updated in, and deleted from a data source such as a database.
 * @package DarlingCms\interfaces\crud
 */
interface IPermissionCrud
{
    public function create(IPermission $permission): bool;

    public function read(string $permissionName): IPermission;

    public function update(string $permissionName, IPermission $newPermission): bool;

    public function delete(string $permissionName): bool;
}
