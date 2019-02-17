<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 19:23
 */

namespace DarlingCms\classes\user;


use DarlingCms\abstractions\user\APDOCompatibleUser;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;

/**
 * Class User. Defines a simple implementation of th IUser interface. This implementation
 * is designed to play nice with PDO, and can be easily instantiated from the results of
 * a call to the PDOStatement::fetchAll() method that uses the PDO::FETCH_CLASS option.
 *
 * i.e.: PDOStatement::fetchAll(PDO::FETCH_CLASS, '\\DarlingCms\\classes\\user\\User', $ctor_args)
 *
 * WARNING: Do not set the and PDO::FETCH_PROPS_LATE fetch style options. This class expects PDO to
 * instantiate using the $ctor_args parameter to pass the appropriate values to the __construct()* method!
 *
 * i.e.
 *
 * Good : PDOStatement::fetchAll(PDO::FETCH_CLASS, '\\DarlingCms\\classes\\user\\User', [$userName, $userMeta, $userRoles])
 *
 * Bad : PDOStatement::fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, '\\DarlingCms\\classes\\user\\User')
 *
 * *See the APDOCompatibleUser::__construct() method documentation for what parameters are expected.
 *
 * @package DarlingCms\classes\user
 * @see APDOCompatibleUser::__construct()
 */
class User extends APDOCompatibleUser implements IUser
{
    /**
     * Returns the User's user name.
     * @return string The User's user name.
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Returns the User's user id.
     * @return string The User's user id.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Returns an array of the User's public meta data.
     * @return array An array of the User's public meta data.
     */
    public function getPublicMeta(): array
    {
        return $this->meta[self::USER_PUBLIC_META_INDEX];
    }

    /**
     * Returns an array of the User's private meta data.
     * @return array An array of the User's private meta data.
     */
    public function getPrivateMeta(): array
    {
        return $this->meta[self::USER_PRIVATE_META_INDEX];
    }

    /**
     * Returns an array of the roles assigned to the user.
     * @return array|IRole[] An array of roles assigned to the User.
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns true if User is assigned specified role, false otherwise.
     * @param IRole $role The role to check for.
     * @return bool True if User is assigned the specified role, false otherwise.
     */
    public function userHasRole(IRole $role): bool
    {
        /**
         * @devNote
         * in_array()'s strict parameter is explicitly set to false in this method because of how PHP
         * compares objects.
         *
         * When objects are compared strictly, i.e., ===,  the are only equal if they are both of the
         * same instance:
         *
         * $classA = new ClassA();
         * $classACopy = $classA;
         * var_dump($classA === $classACopy); // TRUE | same instance
         *
         * $classB = new ClassB();
         * $classB2 = new ClassB();
         * var_dump($classB === $classB2); // FALSE | same implementation, different instances | THIS IS THE ISSUE WITH STRICT COMPARISON FOR OBJECTS IN THIS METHODS CONTEXT! Same implementation, not same instance, this method should only care about implementation since the provided IRole implementation will most likely be a unique instance, and this method just need to make sure that the provided IRole implementation matches one of the User's assigned IRole implementations, NOT instances!
         *
         * Since this method just needs to check that the provided IRole implementation matches one of the
         * assigned IRole implementations, i.e., same class type, not same instance, comparison must not
         * be strict:
         *
         * $classA = new ClassA();
         * $classACopy = $classA;
         * var_dump($classA == $classACopy); // TRUE
         *
         * $classB = new ClassB();
         * $classB2 = new ClassB();
         * var_dump($classB == $classB2); // TRUE | Same implementation, different instances, this is the case that should be tested, therefore in_array()'s strict parameter MUST be set to false.
         *
         * @see http://php.net/manual/en/language.oop5.object-comparison.php for more on object comparsion in PHP
         */
        return in_array($role, $this->roles, false);
    }
}
