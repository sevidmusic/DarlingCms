<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 19:24
 */

namespace DarlingCms\classes\user;


use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserPassword;

/**
 * Class UserPassword. Defines a simple implementation of the IUserPassword interface.
 * @package DarlingCms\classes\user
 */
class UserPassword implements IUserPassword
{
    private $password;
    private $userId;

    /**
     * UserPassword constructor. Constructs a UserPassword instance for the specified User.
     * @param IUser|null $user The User will password belongs to.
     * @param string|null $password The password.
     * Note: null defaults are used to prevent any errors when instantiating from a PDO query. When
     * using this class in a typical new User() context it is best to supply both parameters.
     */
    public function __construct(IUser $user = null, string $password = null)// @todo parameters should default to empty strings
    {
        /**
         * Check if properties were already set, for instance, by PDO.
         */
        if (isset($user) === true && isset($password) === true) { // @todo should check !empty() instead of isset()
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $this->userId = password_hash($user->getUserId(), PASSWORD_DEFAULT);
        }
    }

    /**
     * Validate a the supplied password matches the user's actual password.
     * @param IUser $user The User.
     * @param string $password The password to validate.
     * @return bool True if password is valid, false otherwise.
     */
    public function validatePassword(IUser $user, string $password): bool
    {
        if (password_verify($user->getUserId(), $this->userId) === true && password_verify($password, $this->password) === true) {
            return true;
        }
        return false;
    }

    public function getHashedPassword(): string
    {
        return $this->password;
    }

    public function getHashedUserId(): string
    {
        return $this->userId;
    }


}
