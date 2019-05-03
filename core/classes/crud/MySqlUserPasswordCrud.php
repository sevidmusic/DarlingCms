<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-28
 * Time: 16:31
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\user\AnonymousUser;
use DarlingCms\classes\user\UserPassword;
use DarlingCms\interfaces\crud\IUserPasswordCrud;
use DarlingCms\interfaces\user\IUser;
use DarlingCms\interfaces\user\IUserPassword;
use PDO;

class MySqlUserPasswordCrud extends AMySqlQueryCrud implements IUserPasswordCrud
{
    const PASSWORD_TABLE_NAME = 'passwords';

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations. Set's the
     * name of the table CRUD operations will be performed on.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     */
    final public function __construct(MySqlQuery $MySqlQuery)
    {
        parent::__construct($MySqlQuery, self::PASSWORD_TABLE_NAME);
    }

    /**
     * Creates a table named using the value of the $tableName property.
     * Note: This method is intended to be called by the __construct() method on instantiation.
     * NOTE: Implementations MUST implement this method in order to insure
     * the __construct() method can create the table used by the
     * implementation if it does not already exist.
     * @return bool True if table was created, false otherwise.
     */
    protected function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            userName VARCHAR(242) NOT NULL UNIQUE,
            password VARCHAR(242) NOT NULL UNIQUE,
            passwordId VARCHAR(242) NOT NULL UNIQUE,
            IPasswordType VARCHAR(242) NOT NULL
        );') === false) {
            error_log('Password Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    public function create(IUserPassword $userPassword): bool
    {
        // make sure a password with same name does not already exist.
        if ($this->passwordExists($userPassword->getUserName()) === true) {
            return false;
        }
        // create user
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (userName, password, passwordId, IPasswordType) VALUES (?,?,?,?)',
            [
                $userPassword->getUserName(),
                $userPassword->getHashedPassword(),
                $userPassword->getHashedPasswordId(),
                $this->formatClassName(get_class($userPassword))
            ]
        );
        return $this->passwordExists($userPassword->getUserName());
    }

    public function read(IUser $user): IUserPassword
    {
        // hint select * from table where userName=user->getUserName()
        if ($this->passwordExists($user->getUserName()) === true) {
            // 1. get password data
            $passwordData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$user->getUserName()])->fetchAll(PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($user, $passwordData['password']);
            // 3. Instantiate the appropriate IAction implementation based on the password data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', $this->getClassName($user->getUserName()), [$user->getUserName()], $ctor_args);
            return array_shift($results);
        }
        return new UserPassword(new AnonymousUser(), password_hash(base64_encode(json_encode($this)), PASSWORD_DEFAULT));
    }

    public function update(IUser $user, IUserPassword $newUserPassword): bool
    {
        if ($this->passwordExists($user->getUserName()) === true) {
            if ($this->delete($user)) {
                return $this->create($newUserPassword);
            }
        }
        return false;
    }

    public function delete(IUser $user): bool
    {
        $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$user->getUserName()]);
        return $this->passwordExists($user->getUserName()) === false;
    }

    /**
     * Determine whether or not a specified password exists.
     * @param string $userName The name of the password to check for.
     * @return bool True if password exists, false otherwise.
     */
    private function passwordExists(string $userName): bool
    {
        $passwordData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE userName=?', [$userName])->fetchAll();
        if (empty($passwordData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Get the fully qualified namespaced classname of the specified password.
     * @param string $userName The name of the password.
     * @return string The fully qualified namespaced classname of the specified password.
     */
    private function getClassName(string $userName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IPasswordType FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$userName])->fetchAll(PDO::FETCH_ASSOC)[0]['IPasswordType'];
    }
}
