<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 17:30
 */

namespace DarlingCms\classes\privilege;


use DarlingCms\interfaces\accessControl\IPermission;
use DarlingCms\interfaces\privilege\IAction;

/**
 * Class Permission. Simple implementation of the IPermission interface.
 * @package DarlingCms\classes\privilege
 */
class Permission implements IPermission
{
    private $permissionName;
    private $actions = array();

    /**
     * Permission constructor. Assigns the specified IAction implementations to the new Permission instance.
     * @param string $permissionName The name to assign to the permission.
     * @param IAction ...$actions IAction implementations to assign to this Permission instance.
     */
    public function __construct(string $permissionName, IAction ...$actions)
    {
        $this->permissionName = $permissionName;
        $this->actions = $actions;
    }


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
