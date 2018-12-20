<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 19:24
 */

namespace DarlingCms\classes\user;


use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserCrud;
use DarlingCms\interfaces\user\IUserPassword;

/**
 * Class UserCrud. Defines an implementation of the IUserCrud interface that uses an injected MySqlQuery
 * instance to perform User related CRUD queries against a MySql Database. This class is responsible for
 * mapping IUser implementations to stored user data in the database. Note that queries are actually
 * handled by the injected ISqlQuery implementation.
 * Note: This class will attempt to create the necessary tables in the database if they do not
 * exist on instantiation.
 *
 * User Table Structure:
 *
 * tableId ! INT NOT NULL PRIMARY KEY AUTO INC
 * userId The user's id.
 * userName The user's name.
 * userMeta The user's meta data.
 * userRoles The names of the role's assigned to the user?
 * userImplementation  The user's fully qualified IUser implementation namespace.
 *
 *
 * @package DarlingCms\classes\user
 */
class UserCrud implements IUserCrud
{
    /**
     * @var string The name of the table that user data is stored in.
     */
    const USER_TABLE_NAME = 'users';
    /**
     * @var string The name of the field where the user's user name is stored in the users table.
     */
    const USER_NAME_FIELD = 'userName';
    /**
     * @var string The name of the field where the user's user id is stored in the users table.
     */
    const USER_ID_FIELD = 'userId';
    /**
     * @var string The name of the field where the user's table id is stored in the users table.
     */
    const TABLE_ID_FIELD = 'tableId';

    /**
     * @var string The name of the field where the user's meta data is stored in the users table.
     */
    const USER_META_FIELD = 'userMeta';
    /**
     * @var string The name of the field where the user's roles data is stored in the users table.
     */
    const USER_ROLES_FIELD = 'userRoles';

    /**
     * @var string The User class's complete namespace.
     */
    const USER_CLASS_NAMESPACE = '\\DarlingCms\\classes\\user\\User';

    /**
     * @var string The IUser interface's complete namespace.
     */
    const USER_IUSER_INTERFACE_NAMESPACE = '\\DarlingCms\\interfaces\\user\\IUser';
    /**
     * @var string The IUser interface's complete namespace.
     */
    const USER_IUSER_PASS_INTERFACE_NAMESPACE = '\\DarlingCms\\classes\\user\\UserPassword';

    /**
     * @var MySqlQuery Injected MySqlQuery instance.
     */
    private $mySqlQuery;

    /**
     * UserCrud constructor. Injects the supplied MySqlQuery instance.
     */
    public function __construct(MySqlQuery $mySqlQuery)
    {
        $this->mySqlQuery = $mySqlQuery;
        if ($this->tableExists(self::USER_TABLE_NAME) === false) {
            $this->generateUserTable();
        }
        if ($this->tableExists(self::PASSWORD_TABLE_NAME) === false) {
            $this->generateUserPasswordTable();
        }
    }

    /**
     * Create a new User.
     * @param IUser $user The IUser implementation instance the represents the user.
     * @param IUserPassword $password The password to assign the user.
     * @return bool True if use was created, false otherwise.
     */
    public function create(IUser $user, IUserPassword $password): bool
    {
        // make sure a user with same username does not already exist.
        if ($this->userExists($user->getUserName()) === true) {
            return false;
        }
        // create user
        $this->mySqlQuery->executeQuery('INSERT INTO ' . self::USER_TABLE_NAME . ' (' . self::USER_ID_FIELD . ', ' . self::USER_NAME_FIELD . ', ' . self::USER_META_FIELD . ', ' . self::USER_ROLES_FIELD . ') VALUES (?,?,?,?)', [$user->getUserId(), $user->getUserName(), $this->packMeta($user), $this->packRoles($user)]);
        // create user password
        $this->mySqlQuery->executeQuery('INSERT INTO ' . self::PASSWORD_TABLE_NAME . ' (' . self::PASSWORD_UID_FIELD . ', ' . self::PASSWORD_PASS_FIELD . ') VALUES (?,?)', [$password->getHashedUserId(), $password->getHashedPassword()]);
        return $this->userExists($user->getUserName());
    }

    /**
     * Read a user from the database.
     *
     * WARNING: In order to conform the the IUser interface this method will always return an IUser implementation
     * instance. However, that does not mean the read was successful. If read() fails to retrieve the user from the
     * database, this method will return an instance of the AnonymousUser class. Consequently, to determine if
     * this method was successful check that the returned IUser implementation is not a AnonymousUser object.
     *
     * i.e.,:
     *
     * if(get_class($UserCrud->read('SomeNonExistentUser')) !== 'DarlingCms\classes\user\AnonymousUser') {...}
     *
     * @param string $userName The user's username.
     * @return IUser The IUser implementation that represents the user.
     */
    public function read(string $userName): IUser
    {
        if ($this->userExists($userName) === true) {
            $results = $this->mySqlQuery->getClass('SELECT * FROM ' . self::USER_TABLE_NAME . ' WHERE ' . self::USER_NAME_FIELD . '=?', self::USER_CLASS_NAMESPACE, [$userName]);
            return array_shift($results);
        }
        return new AnonymousUser();
    }

    public function update(string $userName, string $currentPassword, IUser $newUser, IUserPassword $newPassword): bool
    {
        if ($this->userExists($userName) === true) {
            // Get users password if it exist and the users credentials are valid (i.e.,current username/current password)
            $currentPassword = $this->getUserPassword($userName, $currentPassword);
            // Update user data
            $this->updateUserData($userName, $newUser);
            // update password
            $this->updateUserPasswordData($currentPassword, $newPassword);
            return true; // @todo ! check db to see if new user credentials match, only then return true
        }
        return false;
    }

    public function delete(string $userName, string $currentPassword): bool
    {
        if ($this->userExists($userName) === true) {
            $userPass = $this->getUserPassword($userName, $currentPassword);
            $this->mySqlQuery->executeQuery('DELETE FROM ' . self::USER_TABLE_NAME . ' WHERE ' . self::USER_NAME_FIELD . '=?', [$userName]);
            $this->mySqlQuery->executeQuery('DELETE FROM ' . self::PASSWORD_TABLE_NAME . ' WHERE ' . self::PASSWORD_UID_FIELD . '=?', [$userPass->getHashedUserId()]);
            return $this->userExists($userName) === false;
        }
        return false;
    }

    private function updateUserData(string $userName, IUser $user): void // @todo :bool
    {
        // update user
        $this->mySqlQuery->executeQuery(
            'UPDATE ' . self::USER_TABLE_NAME .
            ' SET ' .
            self::USER_ID_FIELD . '=?, ' .
            self::USER_NAME_FIELD . '=?, ' .
            self::USER_META_FIELD . '=?, ' .
            self::USER_ROLES_FIELD . '=?' .
            ' WHERE ' .
            self::USER_NAME_FIELD . '=?',
            [
                $user->getUserId(),
                $user->getUserName(),
                $this->packMeta($user),
                $this->packRoles($user),
                $userName
            ]
        );
    }

    /**
     * Pack the User's meta data for storage.
     * @param IUser $user
     * @return string
     */
    final private function packMeta(IUser $user): string
    {
        return json_encode([ // @todo Implement private method makeMetaArray(array $public, array $private): array : returns an array structured as follows : array(publicMeat => array(), privateMeta => array())
            $user->getPublicMeta(),
            $user->getPrivateMeta()
        ]);
    }

    final private function packRoles(IUser $user): string
    {
        $roles = array();
        foreach ($user->getRoles() as $role) {
            array_push($roles, base64_encode(serialize($role)));
        }
        return json_encode($roles);
    }

    private function userExists(string $userName): bool
    {
        $userResults = $this->mySqlQuery->getClass('SELECT * FROM ' . self::USER_TABLE_NAME . ' WHERE ' . self::USER_NAME_FIELD . '=?', self::USER_CLASS_NAMESPACE, [$userName]);
        return !empty($userResults);
    }

    private function generateUserTable(): bool
    {
        $this->mySqlQuery->executeQuery('CREATE TABLE ' . self::USER_TABLE_NAME . ' (' . self::TABLE_ID_FIELD . ' int auto_increment primary key,' . self::USER_ID_FIELD . ' TEXT NOT NULL, ' . self::USER_NAME_FIELD . ' TEXT NOT NULL, ' . self::USER_META_FIELD . ' TEXT NOT NULL, ' . self::USER_ROLES_FIELD . ' TEXT NOT NULL)');
        return $this->tableExists(self::USER_TABLE_NAME);
    }

    /**
     * Returns true if the specified table exists in the current database, false otherwise.
     * @param string $tableName The name of the table to check for.
     * @return bool true if the specified table exists in the current database, false otherwise.
     * @see https://www.quora.com/How-do-you-check-if-your-table-exists-or-not-in-MySQL
     * @see https://stackoverflow.com/questions/167576/check-if-table-exists-in-sql-server
     */
    private function tableExists(string $tableName)
    {
        $results = $this->mySqlQuery->executeQuery('SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$tableName]);
        return $this->hasResults($results);
    }

    /**
     * Check if there are any results associated with the specified PDOStatement instance.
     * @param \PDOStatement $statement The PDOStatement to check.
     * @return bool True if there are any results, false otherwise.
     */
    private function hasResults(\PDOStatement $statement): bool
    {
        $count = 0;
        foreach ($statement as $result) {
            $count++;
        }
        return $count > 0;
    }

    // password crud //

    /**
     * @var string The name of the table that user password data is stored in.
     */
    const PASSWORD_TABLE_NAME = 'passwords'; // @todo ! *ACTIVE* Move to UserPasswordCrud

    /**
     * @var string The name of the field where the user's id is stored in the passwords table.
     */
    const PASSWORD_UID_FIELD = 'userId'; // @todo ! *ACTIVE* Move to UserPasswordCrud

    /**
     * @var string The name of the field where the user's password is stored in the passwords table.
     */
    const PASSWORD_PASS_FIELD = 'password'; // @todo ! *ACTIVE* Move to UserPasswordCrud

    /**
     * @var string The name of the field where the password's id is stored in the passwords table.
     */
    const PASSWORD_ID_FIELD = 'tableId'; // @todo ! *ACTIVE* Move to UserPasswordCrud


// @todo ! *ACTIVE* Move to UserPasswordCrud
    private function generateUserPasswordTable(): bool
    {
        $this->mySqlQuery->executeQuery('CREATE TABLE ' . self::PASSWORD_TABLE_NAME . ' (' . self::PASSWORD_ID_FIELD . ' int auto_increment primary key, ' . self::PASSWORD_UID_FIELD . ' TEXT NOT NULL, ' . self::PASSWORD_PASS_FIELD . ' TEXT NOT NULL)');
        return $this->tableExists(self::PASSWORD_TABLE_NAME);
    }

    // @todo ! *ACTIVE* Move to PasswordsCrud
    private function updateUserPasswordData(IUserPassword $currentPassword, IUserPassword $newPassword): void // @todo :bool
    {
        $this->mySqlQuery->executeQuery(
            'UPDATE ' . self::PASSWORD_TABLE_NAME . ' SET ' . self::PASSWORD_UID_FIELD . '=?, ' . self::PASSWORD_PASS_FIELD . '=? WHERE ' . self::PASSWORD_UID_FIELD . '=?',
            [
                $newPassword->getHashedUserId(),
                $newPassword->getHashedPassword(),
                $currentPassword->getHashedUserId(),
            ]
        );
    }

    // @todo ! *ACTIVE* Move to UserPasswordCrud
    private function getUserPassword(string $userName, string $currentPassword): IUserPassword
    {
        // get user password
        $passwordClasses = $this->mySqlQuery->getClass('SELECT * FROM ' . self::PASSWORD_TABLE_NAME, self::USER_IUSER_PASS_INTERFACE_NAMESPACE);
        foreach ($passwordClasses as $pass) {
            if ($pass->validatePassword($this->read($userName), $currentPassword) === true) {
                return $pass;
            }
        }
        return new UserPassword(new AnonymousUser(), password_hash(base64_encode(serialize($this)), PASSWORD_DEFAULT));
    }
}
