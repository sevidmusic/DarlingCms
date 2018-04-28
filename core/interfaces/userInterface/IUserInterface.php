<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/28/18
 * Time: 10:02 AM
 */

namespace DarlingCms\interfaces\userInterface;

/**
 * Interface IUserInterface. Defines the basic contract of an object that defines a user interface.
 * @package DarlingCms\interfaces\userInterface
 */
interface IUserInterface
{
    /**
     * Gets the user interface.
     * @return string The user interface.
     */
    public function getUserInterface(): string;
}
