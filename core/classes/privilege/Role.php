<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 17:30
 */

namespace DarlingCms\classes\privilege;


use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\interfaces\privilege\IPermission;
use DarlingCms\interfaces\privilege\IRole;

/**
 * Class Role. Defines a simple implementation of the IRole interface.
 * @package DarlingCms\classes\privilege
 */
class Role extends APDOCompatibleRole implements IRole
{
    /**
     * Returns the name of the role.
     * @return string The name of the role.
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * Returns an array of the permissions assigned to the role.
     * @return array Array of the permissions assigned to the role.
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Returns true if the role is assigned the specified permission, false otherwise.
     * @param IPermission $permission The permission to check for.
     * @return bool True if the role is assigned the specified permission, false otherwise.
     */
    public function hasPermission(IPermission $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
