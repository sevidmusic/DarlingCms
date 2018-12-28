<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 17:30
 */

namespace DarlingCms\classes\privilege;


use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\interfaces\privilege\IAction;
use DarlingCms\interfaces\privilege\IPermission;

/**
 * Class Permission. Simple implementation of the IPermission interface.
 * @package DarlingCms\classes\privilege
 */
class Permission extends APDOCompatiblePermission implements IPermission
{
    /**
     * Returns the permission's name.
     * @return string The permission's name.
     */
    public function getPermissionName(): string
    {
        return $this->permissionName;
    }

    /**
     * Returns an array of the actions assigned to the permission.
     * @return array An array of the actions assigned to the permission.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Returns true if the permission is assigned the specified action, false otherwise.
     * @param IAction $action The action to check for.
     * @return bool True if the permission is assigned the specified action, false otherwise.
     */
    public function hasAction(IAction $action): bool
    {
        return in_array($action, $this->getActions(), true);
    }

}
