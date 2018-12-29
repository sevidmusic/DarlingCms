<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 19:24
 */

namespace DarlingCms\classes\user;


use DarlingCms\abstractions\user\APDOCompatibleUserPassword;
use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserPassword;

/**
 * Class UserPassword. Defines a simple implementation of the IUserPassword interface.
 * @package DarlingCms\classes\user
 */
class UserPassword extends APDOCompatibleUserPassword implements IUserPassword
{
    /**
     * Validate a the supplied password matches the user's actual password.
     * @param IUser $user The User.
     * @param string $password The password to validate.
     * @return bool True if password is valid, false otherwise.
     */
    public function validatePassword(IUser $user, string $password): bool
    {
        if (password_verify($user->getUserId(), $this->passwordId) === true && password_verify($password, $this->password) === true) {
            return true;
        }
        return false;
    }

    public function getHashedPassword(): string
    {
        return $this->password;
    }

    public function getHashedPasswordId(): string
    {
        return $this->passwordId;
    }

    /**
     * Returns the user name of the user this password belongs to.
     * @return string The user name of the user this password belongs to.
     */
    public function getUserName(): string
    {
        return $this->userName;
    }


}
