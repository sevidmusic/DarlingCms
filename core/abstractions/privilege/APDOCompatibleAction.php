<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-28
 * Time: 10:28
 */

namespace DarlingCms\abstractions\privilege;


use DarlingCms\interfaces\privilege\IAction;

abstract class APDOCompatibleAction implements IAction
{
    protected $actionName;
    protected $actionDescription;

    /**
     * Action constructor.
     * @param $actionName
     * @param $actionDescription
     */
    final public function __construct(string $actionName, string $actionDescription)
    {
        $this->actionName = $actionName;
        $this->actionDescription = $actionDescription;
    }

    /**
     * Return's the action's name.
     * @return string The action's name.
     */
    abstract public function getActionName(): string;

    /**
     * Returns the description of the action.
     * @return string The action's description.
     */
    abstract public function getActionDescription(): string;


}
