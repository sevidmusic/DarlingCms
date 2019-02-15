<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-22
 * Time: 10:56
 */

namespace DarlingCms\abstractions\user;


use DarlingCms\classes\crud\MySqlUserCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;

/**
 * Class APDOCompatibleUser. Defines an abstract implementation of the IUser interface
 * that is designed to play nice with PDO and the PDOStatement class's fetchAll() method.
 * Implementations of this abstract class can be instantiated from a fetchAll() call that
 * is structured as follows:
 *
 * $PDOStatement->fetchAll(PDO::FETCH_CLASS, $className, $ctor_args);
 *
 * IMPORTANT: ONLY USE THE FETCH_CLASS OPTION! DO NOT USE FETCH_PROPS_LATE OR PROPERTY VALUE SET BY
 * THE __construct() METHOD MAY BE OVERWRITTEN WHEN PDO SETS IT'S VALUES!!!
 *
 * The $ctor_args array should be structured to match the parameters expected by the __construct() method
 * defined by this abstract class:
 * $ctor_args = array(
 *                     $userName, // the user's user name
 *                     $meta, // the array of the user's public and private meta data indexed by the strings 'public' and 'private', respectively.
 *                     $roles, // the array of IRole implementations assigned to the user.
 *              )
 *
 * Note: The MySqlUserCrud class's read() method does this very thing to instantiate IUser implementations
 * from user data stored in a MySql database.
 *
 * @package DarlingCms\abstractions\user
 * @see \PDO
 * @see \PDOStatement::fetchAll()
 * @see MySqlUserCrud::read()
 */
abstract class APDOCompatibleUser implements IUser
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
     * @var string The user's user name.
     */
    protected $userName;

    /**
     * @var string The user's id.
     */
    protected $userId;

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
    protected $meta = array(self::USER_PUBLIC_META_INDEX => array(), self::USER_PRIVATE_META_INDEX => array());
    /**
     * @var array|IRole[] Array of roles assigned to the user.
     */
    protected $roles = array();

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
     * @param array|IRole[] $roles IRole implementation instances to assign to the User.
     */
    final public function __construct($userName = self::DEFAULT_USERNAME, $meta = array(), array $roles = array())
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
        if (!empty($roles) === true) {
            foreach ($roles as $role) {
                $this->addRole($role);
            }
        }
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
    final protected function generateUserId(): string
    {
        $randString = substr(str_replace(array('\\', '/', '"', "'", '|', '?', '=', '*', '.', ',', '$'), '', password_hash(serialize($this), PASSWORD_DEFAULT)), 3, -4);
        try {
            $randInt = random_int(PHP_INT_MIN, PHP_INT_MAX);
        } catch (\Exception $e) {
            error_log('WARNING: Failed to generate cryptographically secure unique id for user ' . $this->getUserName() . '. A non cryptographically secure unique id has been assigned instead.');
            $randInt = rand(PHP_INT_MIN, PHP_INT_MAX);
        }
        return $randString . str_replace('-', '', strval($randInt));
    }

    final public function __set($name, $value)
    {
        // DO NOT IMPLEMENT OR PDO MAY SET UNDECLARED PROPERTY VALUES!
    }

    final private function addRole(IRole $role)
    {
        array_push($this->roles, $role);
    }

    abstract public function getUserName(): string;

    abstract public function getUserId(): string;

    abstract public function getPublicMeta(): array;

    abstract public function getPrivateMeta(): array;

    abstract public function getRoles(): array;

    abstract public function userHasRole(IRole $role): bool;
}
