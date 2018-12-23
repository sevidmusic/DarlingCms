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
 * a call to the PDO::fetchAll() method that uses the PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE
 * options.
 *
 * i.e.: fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, ''\\DarlingCms\\classes\\user\\User'')
 *
 * WARNING: This class will only play nice with PDO if the PDO::FETCH_CLASS and PDO::FETCH_PROPS_LATE
 * options are set!
 * Note: Implementations of this class that wish to modify the property values set by PDO from the query results
 * should provide their own implementation of the __set() method.
 * @package DarlingCms\classes\user
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
     * @return array An array of roles assigned to the User.
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
        return in_array($role, $this->roles, true);
    }
}
