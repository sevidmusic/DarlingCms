<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-28
 * Time: 03:56
 */

namespace DarlingCms\abstractions\privilege;


use DarlingCms\interfaces\privilege\IAction;
use DarlingCms\interfaces\privilege\IPermission;

abstract class APDOCompatiblePermission implements IPermission
{
    protected $permissionName;
    protected $actions = array();

    /**
     * Permission constructor. Assigns the specified IAction implementations to the new Permission instance.
     * @param string $permissionName The name to assign to the permission.
     * @param array|IAction[] $actions IAction implementations to assign to this Permission instance.
     */
    public function __construct(string $permissionName, array $actions)
    {
        $this->permissionName = $permissionName;
        $this->actions = $actions;
    }

    final protected function addAction(IAction $action)
    {
        array_push($this->actions, $action);
    }

    /**
     * Returns the permission's name.
     * @return string The permission's name.
     */
    abstract public function getPermissionName(): string;

    /**
     * Returns an array of the actions assigned to the permission.
     * @return array|IAction[] An array of the actions assigned to the permission.
     */
    abstract public function getActions(): array;

    /**
     * Returns true if the permission is assigned the specified action, false otherwise.
     * @param IAction $action The action to check for.
     * @return bool True if the permission is assigned the specified action, false otherwise.
     */
    abstract public function hasAction(IAction $action): bool;

    /**
     * Implement empty set method since this object may be instantiated from a PDO query result, leaving
     * this method empty will prevent PDO from automatically setting undefined properties as public properties.
     */
    public function __set($name, $value)
    {
        /**
         * IMPORTANT! THIS METHOD MUST REMAIN EMPTY UNLESS YOU ARE COMPLETELY SURE YOU KNOW WHAT YOU ARE DOING
         * AND ARE WILLING TO ACCEPT THE CONSEQUENCES OF WHAT YOU ARE DOING!!! THE REASON THIS METHOD IS EMPTY
         * IS TO PREVENT ANY UNDECLARED PROPERTIES FROM BEING CREATED AND SET BY ACTORS OTHER THAN THIS CLASS.
         */
    }
}
