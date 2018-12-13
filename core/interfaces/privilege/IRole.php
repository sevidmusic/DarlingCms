<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 15:08
 */

namespace DarlingCms\interfaces\privilege;


interface IRole
{
    /**
     * Returns the name of the role.
     * @return string The name of the role.
     */
    public function getRoleName(): string;

    /**
     * Returns an array of the permissions assigned to the role.
     * @return array Array of the permissions assigned to the role.
     */
    public function getPermissions(): array;

    /**
     * Returns true if the role is assigned the specified permission, false otherwise.
     * @param IPermission $permission The permission to check for.
     * @return bool True if the role is assigned the specified permission, false otherwise.
     */
    public function hasPermission(IPermission $permission): bool;
}
