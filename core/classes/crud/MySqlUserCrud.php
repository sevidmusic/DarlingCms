<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 11:58
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\user\AnonymousUser;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\crud\IUserCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;

/**
 * Class MySqlUserCrud. Defines an implementation of the IUserCrud interface
 * that extends the AMySqlQueryCrud abstract class. This implementation can be used
 * to perform CRUD operations on user data in a MySql database.
 * @package DarlingCms\classes\crud
 * @see
 */
class MySqlUserCrud extends AMySqlQueryCrud implements IUserCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const USER_TABLE_NAME = 'users';

    /**
     * @var IRoleCrud Injected IRoleCrud implementation instance. This object is used to perform CRUD
     *                operations on the user's assigned IRole implementation data in the database.
     */
    private $roleCrud;

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations on user data.
     * Sets the RoleCrud instance used for CRUD operations on role data.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     */
    public function __construct(MySqlQuery $MySqlQuery, IRoleCrud $roleCrud)
    {
        parent::__construct($MySqlQuery, self::USER_TABLE_NAME);
        $this->roleCrud = $roleCrud;
    }

    /**
     * Create a new user.
     * Note: This method will return false if the user already exists.
     * @param IUser $user The IUser implementation instance that represents the user to create.
     * @return bool True if user was created, false otherwise.
     */
    public function create(IUser $user): bool
    {
        if ($this->userExists($user->getUserName()) === true) {
            return false;
        }
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (userId, userName, userMeta, userRoles, IUserType) VALUES (?,?,?,?,?)',
            $this->packUserData($user)
        );
        return $this->userExists($user->getUserName());

    }

    private function packUserData(IUser $user): array
    {
        return array(
            $user->getUserId(),
            $user->getUserName(),
            $this->packMeta($user),
            $this->packRoles($user),
            $this->formatClassName(get_class($user))
        );
    }

    /**
     * Read a specified user from the database, and return the appropriate IUser interface implementation
     * for the user using the user's data to populate the implementation's properties.
     * @param string $userName Name of the user to read.
     * @return IUser The appropriate IUser implementation instance for the specified user.
     *
     * WARNING: This method will return an instance of the AnonymousUser() class if the specified
     * user can't be found. This is done to conform to the IUserCrud interface, which requires that
     * implementations of the read() method return an instance of an IUser implementation.
     */
    public function read(string $userName): IUser
    {
        if ($this->userExists($userName) === true) {
            /* @devNote:
             * Unpack user data here so it can be passed to the IUser implementation's
             * __construct method, this is better then using PDO::FETCH_PROPS_LATE and relying an
             * implementation of the __set() method to handle logic that should be run after PDO
             * sets the relevant property values. This way the class is constructed as usual,
             * with the responsibility of instantiation logic assigned to the __construct() method.
             */
            $userData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$userName])->fetchAll(\PDO::FETCH_ASSOC)[0];
            $ctor_args = array($userData['userName'], $this->unpackMeta($userData['userMeta']), $this->unpackRoles($userData['userRoles']));
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', $this->getClassName($userName), [$userName], $ctor_args);
            return array_shift($results);
        }
        return new AnonymousUser();
    }

    /**
     * @return array|IUser[]
     */
    public function readAll(): array
    {
        $userNames = $this->MySqlQuery->executeQuery('SELECT userName FROM ' . $this->tableName)->fetchAll(\PDO::FETCH_ASSOC);
        $users = array();
        foreach ($userNames as $userName) {
            array_push($users, $this->read($userName['userName']));
        }
        return $users;
    }


    /**
     * Unpack the user's meta data.
     * @param string $packedMeta The packed meta data.
     * @return array Array of user's public and private meta data indexed by the strings 'public' and
     *               'private', respectively.
     */
    final private function unpackMeta(string $packedMeta): array
    {
        return json_decode($packedMeta, true);
    }

    /**
     * Unpack the user's roles.
     * Note: This method uses the injected IRoleCrud implementation to read and assign
     * the IRole implementations named in the $packedRoles array.
     * @param string $packedRoles The packed roles.
     * @return array|IRole[] Array of IRole implementation instances assigned to the user.
     */
    final private function unpackRoles(string $packedRoles): array
    {
        $roles = array();
        $roleNames = json_decode($packedRoles, true);
        foreach ($roleNames as $roleName) {
            array_push($roles, $this->roleCrud->read($roleName));
        }
        return $roles;
    }

    /**
     * Update a specified user's data from an IUser implementation instance.
     * NOTE: This method will not perform update if new user's username does not match the specified user's username.
     * @param string $userName The user to update's user name.
     * @param IUser $newUser The IUser implementation instance that represents the new user data.
     * @return bool True if update succeeded, false otherwise.
     */
    public function update(string $userName, IUser $newUser): bool
    {
        if ($this->userExists($userName) === true && $userName === $newUser->getUserName()) {
            if ($this->delete($userName) === true) {
                return $this->create($newUser);
            }
        }
        /**
         * @devNote:
         * The following code has the potential to cause duplicate entry violations... so until a way to
         * use SQL's UPDATE that does not have this risk is found, rows will always be deleted and then
         * re-created when updating a user.
         * if ($this->userExists($userName) === true) {
         * $params = $this->packUserData($newUser);
         * array_push($params, $userName);
         * $this->MySqlQuery->executeQuery('UPDATE ' . $this->tableName . ' SET userId=?, userName=?, userMeta=?, userRoles=?, IUserType=?  WHERE userName=? LIMIT 1', $params);
         * return $this->userExists($newUser->getUserName());
         * }
         **/
        return false;
    }

    public function delete(string $userName): bool
    {
        if ($this->userExists($userName) === true) {
            $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE userName=?', [$userName]);
            return ($this->userExists($userName) === false);
        }
        return false;
    }

    /**
     * Creates the users table.
     * @return bool True if users table was created, false otherwise.
     */
    final protected function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            userId VARCHAR(256) NOT NULL UNIQUE,
            userName VARCHAR(256) NOT NULL UNIQUE,
            userMeta JSON NOT NULL,
            userRoles JSON NOT NULL,
            IUserType VARCHAR(256) NOT NULL
        );') === false) {
            error_log('User Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    /**
     * Determines if the specified user exists.
     * @param string $userName The user to check for.
     * @return bool True if user exists, false otherwise.
     */
    final protected function userExists(string $userName): bool
    {
        $userData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE userName=?', [$userName])->fetchAll();
        if (empty($userData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Pack the User's meta data for storage. This method will create
     * an array from the user's meta data, and will then encode the
     * generated array as json. The array is constructed as follows:
     * array(
     *     'public' => $user->getPublicMeta(),
     *     'private' => $user->getPrivateMeta()
     * )
     * Note: This array is packed by encoding it as json via json_encode().
     * @param IUser $user The user whose meta data is to be packed.
     * @return string The packed meta data.
     */
    final private function packMeta(IUser $user): string
    {
        return json_encode([
            'public' => $user->getPublicMeta(),
            'private' => $user->getPrivateMeta()
        ]);
    }

    /**
     * Creates an array of the names of the IRole implementations assigned to the user,
     * and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         'SomeRole', // when this user is read, the read method will use the IRoleCrud implementation to look for a role name SomeRole in the database, if it finds such a role it will be assigned as one of the user's roles via the $ctor_args array.
     *         'SomeOtherRole',
     *         'AFinalRole',
     *     ]
     * }
     * @param IUser $user The user whose roles should be packed.
     * @return string The packed roles.
     */
    final private function packRoles(IUser $user): string
    {
        $roles = array();
        foreach ($user->getRoles() as $role) {
            array_push($roles, $role->getRoleName());
        }
        return json_encode($roles);
    }

    /**
     * Get the class name including the fully qualified namespace of the specified user in the following format:
     * \\Some\\Name\\Spaced\\ClassName
     * @param string $userName
     * @return string
     */
    final private function getClassName(string $userName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IUserType FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$userName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IUserType'];
    }
}
