<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 17:30
 */

namespace DarlingCms\classes\privilege;

use DarlingCms\interfaces\privilege\IAction;

/**
 * Class Action. Defines a simple implementation of the IAction interface.
 * @package DarlingCms\classes\privilege
 */
class Action implements IAction
{
    private $actionName;
    private $actionDescription;

    /**
     * Action constructor.
     * @param $actionName
     * @param $actionDescription
     */
    public function __construct(string $actionName, string $actionDescription)
    {
        $this->actionName = $actionName;
        $this->actionDescription = $actionDescription;
    }


    /**
     * Return's the action's name.
     * @return string The action's name.
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * Returns the description of the action.
     * @return string The action's description.
     */
    public function getActionDescription(): string
    {
        return $this->actionDescription;
    }

}
