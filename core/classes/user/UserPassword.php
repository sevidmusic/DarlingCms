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
        if (password_verify($password, $this->password) === true) {
            return true;
        }
        /**
         * NOTE: Checking the password id as well as the password is ideal however, this behavior currently breaks
         * if user has been modified. This is because the passwordId is just a hashed version of the userId, which
         * in turn, is a randomly generated string based on a serialized version of the User as it was when it was
         * originally created. @see APDOCompatibleUser::generateUserId()
         *
         * If a user is created, then updated later, the hashed password id will always fail to match the
         * updated user as it was based on the state of the user before the updates occurred.
         *
         * Now, this is quite annoying because it is really nice to have a second test via the hashed passwordId,
         * but the UserPassword class MUST NOT be aware of the implementation details of a User. So until a work
         * around that does NOT involve refactoring the User and UserPassword classes to be more tightly coupled,
         * the fix is to remove the passwordId check.
         *
         * Removing the passwordId check fixes the issue as the password is just a hash of the users actual password
         * which does not changed whenever a stored User is updated.
         * if (password_verify($user->getUserId(), $this->passwordId) === true && password_verify($password, $this->password) === true) {return true;}
         */
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
