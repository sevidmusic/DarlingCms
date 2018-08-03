<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 7/30/18
 * Time: 4:50 PM
 */

namespace DarlingCms\interfaces\processor;


use DarlingCms\interfaces\accessControl\IUser;

/**
 * Interface IUserLogin. Defines the basic contract of an object that processes user login.
 * @package DarlingCms\interfaces\processor
 */
interface IUserLogin
{
    /**
     * Login a user.
     * @param IUser $user The IUser implementation instance that represents the user to login.
     * @return bool True if user was logged in successfully, false otherwise.
     */
    public function login(IUser $user): bool;

    /**
     * Logout a user.
     * @param IUser $user The IUser implementation instance that represents the user to logout.
     * @return bool True if user was logged in successfully, false otherwise.
     */
    public function logout(IUser $user): bool;

    /**
     * Check if a user is logged in.
     * @param IUser $user The IUser implementation instance that represents the user whose login state
     *                    is being checked.
     * @return bool True if user is logged in, false otherwise.
     */
    public function isLoggedIn(IUser $user): bool;
}
