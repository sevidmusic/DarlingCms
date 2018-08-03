<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 7/27/18
 * Time: 11:40 PM
 */

namespace DarlingCms\interfaces\accessControl;

/**
 * Interface IUser. Defines the basic contract of an object that represents a User.
 * @package DarlingCms\interfaces\accessControl
 */
interface IUser
{
    /**
     * Returns the user's username.
     * @return string The user's username.
     */
    public function getUsername(): string;

    /**
     * Validate the specified password matches the user's password.
     * @param string $password The password to compare with the user's actual password.
     * @return bool True if specified password matches the user's actual password, false otherwise.
     */
    public function validatePassword(string $password): bool;

}
