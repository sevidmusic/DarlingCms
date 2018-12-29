<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-28
 * Time: 16:09
 */

namespace DarlingCms\abstractions\user;


use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserPassword;

abstract class APDOCompatibleUserPassword implements IUserPassword
{

    protected $password;
    protected $passwordId;
    protected $userName;

    /**
     * UserPassword constructor. Constructs a UserPassword instance for the specified User.
     * @param IUser|null $user The User the password belongs to.
     * @param string|null $password The password.
     * Note: null defaults are used to prevent any errors when instantiating from a PDO query. When
     * using this class in a typical new User() context it is best to supply both parameters.
     */
    public function __construct(IUser $user, string $password)
    {
        /**
         * Check if properties were already set externally, for instance, by results of a
         * PDOStatement::fetchAll(PDO::FETCH_CLASS) call. DO NOT use parameter values to set properties
         * if properties are already set, or else the values set externally, for instance by PDO, may be
         * overwritten!
         */
        if (!isset($this->password) === true && !isset($this->passwordId) === true) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $this->passwordId = password_hash($user->getUserId(), PASSWORD_DEFAULT);
            $this->userName = $user->getUserName();
        }
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        // Leave empty to prevent PDO from writing undeclared property values.
    }

    /**
     * Validate a password matches the users actual password.
     * @param IUser $user The IUser implementation .
     * @param string $password The password to validate.
     * @return bool True if supplied password matches the User's password, false otherwise.
     */
    abstract public function validatePassword(IUser $user, string $password): bool;

    abstract public function getHashedPassword(): string;

    abstract public function getHashedPasswordId(): string;

    /**
     * Returns the user name of the user this password belongs to.
     * @return string The user name of the user this password belongs to.
     */
    abstract public function getUserName(): string;

}
