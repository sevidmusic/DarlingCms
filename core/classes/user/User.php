<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 19:23
 */

namespace DarlingCms\classes\user;


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
class User implements IUser
{
    /**
     * @var string Default username if one is not provided to the __construct() method.
     */
    const DEFAULT_USERNAME = 'Anonymous';
    /**
     * @var string Index that should be assigned to the array of public meta data in the $meta property's array.
     */
    const USER_PUBLIC_META_INDEX = 'public';
    /**
     * @var string Index that should be assigned to the array of private meta data in the $meta property's array.
     */
    const USER_PRIVATE_META_INDEX = 'private';
    /**
     * @var string Name of the column that user meta data is expected to be stored in in a Database.
     * @see User::__set()
     */
    const USER_META_COLUMN_NAME = 'userMeta';

    const USER_ROLES_COLUMN_NAME = 'userRoles';

    const IROLE_INTERFACE_NAMESPACE = '\\DarlingCms\\interfaces\\privilege\\IRole';

    /**
     * @var string The user's user name.
     */
    private $userName;

    /**
     * @var string The user's id.
     */
    private $userId;

    /**
     * @var array Array of user meta data. Public and private meta data is organized into two
     *            separate sub arrays indexed by the following constants, respectively:
     *            User::USER_PUBLIC_META_INDEX
     *            User::USER_PRIVATE_META_INDEX
     * i.e.:
     * array(
     *    [User::USER_PUBLIC_META_INDEX] => array( ...array of public meta data...)
     *    [User::USER_PRIVATE_META_INDEX] => array( ...array of private meta data...)
     *)
     */
    private $meta = array(self::USER_PUBLIC_META_INDEX => array(), self::USER_PRIVATE_META_INDEX => array());
    /**
     * @var array|IRole[] Array of roles assigned to the user.
     */
    private $roles = array();

    /**
     * User constructor. Sets the User instance's user name, public and private meta data, and assigns the
     * specified roles.
     * @param string $userName The name to assign to the User. Defaults to User::DEFAULT_USERNAME
     * @param array $meta Array of meta data to assign to the user. This accepts an array of arrays structured
     *                    as follows:
     *                      array(
     *                           [User::USER_PUBLIC_META_INDEX] => array( ...array of public meta data...)
     *                           [User::USER_PRIVATE_META_INDEX] => array( ...array of private meta data...)
     *                      )
     * @param IRole ...$roles IRole implementation instances to assign to the User.
     */
    public function __construct($userName = self::DEFAULT_USERNAME, $meta = array(), IRole ...$roles)
    {
        if ($userName !== self::DEFAULT_USERNAME) {
            $this->userName = $userName;
        }
        if (isset($meta[self::USER_PUBLIC_META_INDEX]) === true && !empty($meta[self::USER_PUBLIC_META_INDEX]) === true) {
            $this->meta[self::USER_PUBLIC_META_INDEX] = $meta[self::USER_PUBLIC_META_INDEX];
        }
        if (isset($meta[self::USER_PRIVATE_META_INDEX]) === true && !empty($meta[self::USER_PRIVATE_META_INDEX]) === true) {
            $this->meta[self::USER_PRIVATE_META_INDEX] = $meta[self::USER_PRIVATE_META_INDEX];
        }
        $this->roles = $roles;
        /**
         * Check if already set, this prevents the $userId property value set by PDO from being overwritten when
         * this class is instantiated from the results of a PDO query.
         */
        if (isset($this->userId) === false) {
            $this->userId = $this->generateUserId();
        }
    }

    /**
     * Generates a unique user id for this user.
     * @return string
     */
    private function generateUserId(): string
    {
        return substr(str_replace(array('\\', '/', '"', "'", '|', '?', '=', '*', '.', ',', '$'), '', password_hash(serialize($this), PASSWORD_DEFAULT)), 3, -4);
    }

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

    final private function unpackRoles(string $packedRoles)
    {
        $encodedRolesArr = json_decode($packedRoles, true);
        foreach ($encodedRolesArr as $encodedRole) {
            $this->addRole(unserialize(base64_decode($encodedRole, true)));
        }
    }

    final private function addRole(IRole $role): void
    {
        array_push($this->roles, $role);
    }

    final private function unpackMeta(string $packedMeta)
    {
        $meta = json_decode($packedMeta, true); // @todo ! Validate json!!!
        // @todo ! Important: Document to  behavior of the logic below as it may have security implications when false|default|: condition is met, i.e. when array_shift() is used.
        $this->meta[self::USER_PUBLIC_META_INDEX] = (isset($meta[self::USER_PUBLIC_META_INDEX]) === true ? $meta[self::USER_PUBLIC_META_INDEX] : array_shift($meta));
        $this->meta[self::USER_PRIVATE_META_INDEX] = (isset($meta[self::USER_PRIVATE_META_INDEX]) === true ? $meta[self::USER_PRIVATE_META_INDEX] : array_shift($meta));
    }

    /**
     * This method is run when writing data to inaccessible properties. This implementation
     * is designed to allow the User class play nice with PDO. Logic implemented in this method
     * is intended to be run after PDO populates class properties from the query results.
     * This method does not need to be called directly, nor should it be.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php#object.set
     * @param string $name The name of the value.
     * @param string $value The value.
     */
    public function __set($name, $value)
    {
        if ($name === self::USER_META_COLUMN_NAME) {
            $this->unpackMeta($value);
        }
        if ($name === self::USER_ROLES_COLUMN_NAME) {
            $this->unpackRoles($value);
        }
    }

}
