<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 15:07
 */

namespace DarlingCms\interfaces\privilege;


interface IAction
{
    /**
     * Return's the action's name.
     * @return string The action's name.
     */
    public function getActionName(): string;

    /**
     * Returns the description of the action.
     * @return string The action's description.
     */
    public function getActionDescription(): string;

}
