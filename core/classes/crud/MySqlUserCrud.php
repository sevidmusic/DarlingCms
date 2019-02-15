<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-21
 * Time: 11:58
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlUserCrud;
use DarlingCms\classes\user\AnonymousUser;
use DarlingCms\interfaces\crud\IUserCrud;
use DarlingCms\interfaces\user\IUser;
use SplSubject;

/**
 * Class MySqlUserCrud. Defines an implementation of the IUserCrud interface
 * that extends the AMySqlQueryCrud abstract class. This implementation can be used
 * to perform CRUD operations on user data in a MySql database.
 * @package DarlingCms\classes\crud
 * @see
 * @todo Define abstract class AMySqlUserCrud which will define the SPL Observer/Subject related methods, properties, and constants
 */
class MySqlUserCrud extends AMySqlUserCrud implements IUserCrud, SplSubject
{
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
     * Update a specified user's data from an IUser implementation instance.
     * NOTE: This method will not perform update if new user's username does not match the specified user's username.
     * @param string $userName The user to update's user name.
     * @param IUser $newUser The IUser implementation instance that represents the new user data.
     * @return bool True if update succeeded, false otherwise.
     */
    public function update(string $userName, IUser $newUser): bool
    {
        // if user exists, & the new user name matches the original, proceed. @devNote: If observer pattern works, it may be possible to allow username to be changed, at the moment changing the user name is prohibited @todo Once observer pattern works look into possibly allowing user name to be changed, though there are many reasons this may not be desired in a system that has many users since the user name is one of the unique identifiers that identifies a user.
        if ($this->userExists($userName) === true && $userName === $newUser->getUserName()) {
            $this->setNotice(self::MOD_TYPE_UPDATE, $userName, $newUser); // @devNote: observer/subject related logic
            if ($this->delete($userName) === true && $this->create($newUser) === true) {
                $this->notify(); // @devNote: observer/subject related logic
                return true;
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
            // only set notice data if $modType is MOD_TYPE_DELETE, otherwise this could break updates as this method is called from within the update() method.
            if ($this->modType !== self::MOD_TYPE_UPDATE) { // @devNote: observer/subject related logic
                $this->setNotice(self::MOD_TYPE_DELETE, $userName, $this->read($userName)); // @devNote: observer/subject related logic
            }
            $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE userName=?', [$userName]);
            if ($this->modType === self::MOD_TYPE_DELETE) { // Only notify of deletion if mod type is MOD_TYPE_DELETE, no need to do it when mod type is MOD_TYPE_UPDATE since user is not actually being permanently deleted, i.e. changes that need to happen when a delete occurs MUST NOT happen when update occurs
                $this->notify();
            }
            return $this->userExists($userName) === false;
        }
        return false;
    }
}
