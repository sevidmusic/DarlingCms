<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-13
 * Time: 18:01
 */

namespace DarlingCms\classes\accessControl;


use DarlingCms\classes\crud\SessionCrud;
use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserLogin;
use DarlingCms\interfaces\user\IUserPassword;

class UserLogin extends SessionCrud implements IUserLogin
{
    const CURRENT_USER_POST_VAR_NAME = 'currentUser';
    const USER_PASSWORD_VAR_NAME = 'password';

    public function login(IUser $user, IUserPassword $password): bool
    {
        $submittedPassword = (empty(filter_input(INPUT_POST, self::USER_PASSWORD_VAR_NAME)) === false ? filter_input(INPUT_POST, self::USER_PASSWORD_VAR_NAME) : '');
        if ($password->validatePassword($user, $submittedPassword) === true && $this->isLoggedIn($user->getUserName()) === false) {
            return $this->create(self::CURRENT_USER_POST_VAR_NAME, $user->getUserName()); // for security we only store the user's user name, any component that needs to say, verify a user's role, will need to use an instance of a IUserCrud implementation to read the user from storage and validate against the stored user, this also makes it impossible to say spoof a user which could be done if the entire user object was stored in the session, which could also expose private user meta data to a hijacked session.
        }
        // @todo : Force input via POST by changing to INPUT_POST once out of dev
        return false;
    }

    public function logout(string $username): bool
    {
        return $this->delete(self::CURRENT_USER_POST_VAR_NAME);
    }

    public function isLoggedIn(string $userName): bool
    {
        if ($this->read(self::CURRENT_USER_POST_VAR_NAME) === $userName) {
            return true;
        }
        return false;
    }

}
