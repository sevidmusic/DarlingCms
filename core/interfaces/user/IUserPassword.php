<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:29
 */

namespace DarlingCms\interfaces\user;

/**
 * Interface IUserPassword. Defines the contract of an object that represents a User's password.
 * @package DarlingCms\interfaces\user
 */
interface IUserPassword
{
    /**
     * Validate a password matches the users actual password.
     * @param IUser $user The IUser implementation .
     * @param string $password The password to validate.
     * @return bool True if supplied password matches the User's password, false otherwise.
     */
    public function validatePassword(IUser $user, string $password): bool;

    public function getHashedPassword():string ;

    public function getHashedPasswordId():string ;

    /**
     * Returns the user name of the user this password belongs to.
     * @return string The user name of the user this password belongs to.
     */
    public function getUserName(): string ;
}
