<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 15:07
 */

namespace DarlingCms\interfaces\accessControl;


use DarlingCms\interfaces\privilege\IAction;

interface IPermission
{
    /**
     * Returns the permission's name.
     * @return string The permission's name.
     */
    public function getPermissionName(): string;

    /**
     * Returns an array of the actions assigned to the permission.
     * @return array An array of the actions assigned to the permission.
     */
    public function getActions(): array;

    /**
     * Returns true if the permission is assigned the specified action, false otherwise.
     * @param IAction $action The action to check for.
     * @return bool True if the permission is assigned the specified action, false otherwise.
     */
    public function hasAction(IAction $action):bool;



}
