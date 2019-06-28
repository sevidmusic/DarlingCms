<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-20
 * Time: 19:37
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\privilege\IPermission;

/**
 * Interface IPermissionCrud. Defines the basic contract of an object that is responsible
 * for mapping IPermission implementation instances so they can be created in, read from,
 * updated in, and deleted from a data source such as a database.
 *
 * @package DarlingCms\interfaces\crud
 *
 * @see IPermissionCrud
 * @see IPermissionCrud::create()
 * @see IPermissionCrud::read()
 * @see IPermissionCrud::readAll()
 * @see IPermissionCrud::update()
 * @see IPermissionCrud::delete()
 */
interface IPermissionCrud
{
    /**
     * Creates a new permission.
     *
     * @param IPermission $permission The IPermission implementation instance that
     *                                represents the permission being created.
     *
     * @return bool True if permission was created, false otherwise.
     */
    public function create(IPermission $permission): bool;

    /**
     * Read a specified permission.
     *
     * @param string $permissionName The name of the permission to read.
     *
     * @return IPermission An IPermission implementation instance that represents
     *                     the specified permission, or, if the specified permission
     *                     does not exist, an IPermission implementation instance that
     *                     represents a default permission with no assigned actions.
     *
     * @devNote: To insure the integrity of the larger permission system,
     *           if the specified permission does not exist, implementations
     *           MUST return a default IPermission implementation instance
     *           that has no actions assigned to it to insure that the default
     *           permission does not allow for unintended privilege escalation
     *           if the specified permission does not exist!
     */
    public function read(string $permissionName): IPermission;

    /**
     * Returns an array of IPermission implementation instances for all
     * stored permissions.
     *
     * @return array|IPermission An array of IPermission implementation instances for all
     *                           stored permissions.
     */
    public function readAll(): array;

    /**
     * Updates the specified permission.
     *
     * @param string $permissionName The name of the permission to update.
     *
     * @param IPermission $newPermission The IPermission implementation instance that
     *                                   represents the updated permission.
     *
     * @return bool True if permission was updated, false otherwise.
     */
    public function update(string $permissionName, IPermission $newPermission): bool;

    /**
     * Deletes the specified permission.
     *
     * @param string $permissionName The name of the permission to delete.
     *
     * @return bool True if the permission was deleted, false otherwise.
     */
    public function delete(string $permissionName): bool;
}
