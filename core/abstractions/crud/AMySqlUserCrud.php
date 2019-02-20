<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-08
 * Time: 00:44
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\observer\crud\MySqlUserCrudObserver;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\crud\IUserCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;
use SplSubject;
use SplObserver;
use SplObjectStorage;

abstract class AMySqlUserCrud extends AMySqlQueryCrud implements IUserCrud, SplSubject
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const USER_TABLE_NAME = 'users';

    /**
     * @var string The target user's user name.
     */
    public $targetUser;

    /**
     * @var IUser The IUser implementation instance that represents the user's data.
     */
    public $user;

    /**
     * @var \SplObjectStorage
     */
    protected $observers; // @devNote: observer/subject related property

    /**
     * @var IRoleCrud Injected IRoleCrud implementation instance. This object is used to perform CRUD
     *                operations on the user's assigned IRole implementation data in the database.
     */
    private $roleCrud; // @devNote: observer/subject related property

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used for CRUD operations on user data.
     * Sets the RoleCrud instance used for CRUD operations on role data.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     */
    public function __construct(MySqlQuery $MySqlQuery, IRoleCrud $roleCrud)
    {
        parent::__construct($MySqlQuery, self::USER_TABLE_NAME);
        $this->observers = new SplObjectStorage();
        $this->observers->attach(new MySqlUserCrudObserver());
        $this->roleCrud = $roleCrud;
    }

    /**
     * Attach an SplObserver
     * @link https://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }


    /**
     * Detach an observer
     * @link https://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * Notify an observer
     * @link https://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function notify(): void
    {
        /** @var \SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Create a new user.
     * @param IUser $user The IUser implementation instance that represents the user.
     * @return bool True if user was created, false otherwise.
     */
    abstract public function create(IUser $user): bool;

    /**
     * Read a specified user's data.
     * @param string $userName The user's username.
     * @return IUser The IUser implementation instance that represents the user.
     */
    abstract public function read(string $userName): IUser;


    /**
     * Return all stored Users.
     * @return array|IUser[] Array of all stored users.
     */
    abstract public function readAll(): array;

    /**
     * Update a specified user's data.
     * @param string $userName The user's username.
     * @param IUser $newUser The IUser implementation instance that represents the user's new data.
     * @return bool True if user was update, false otherwise.
     */
    abstract public function update(string $userName, IUser $newUser): bool;

    /**
     * Delete a specified user's data.
     * @param string $userName The user's username.
     * @return bool True if user was deleted, false otherwise.
     */
    abstract public function delete(string $userName): bool;

    /**
     * Creates the users table.
     * @return bool True if users table was created, false otherwise.
     */
    final protected function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            userId VARCHAR(242) NOT NULL UNIQUE,
            userName VARCHAR(242) NOT NULL UNIQUE,
            userMeta TEXT NOT NULL,
            userRoles TEXT NOT NULL,
            IUserType VARCHAR(242) NOT NULL
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
     * Get the class name including the fully qualified namespace of the specified user in the following format:
     * \\Some\\Name\\Spaced\\ClassName
     * @param string $userName
     * @return string
     */
    final protected function getClassName(string $userName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IUserType FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$userName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IUserType'];
    }

    /**
     * Set the values of the properties that are used by observers of this subject.
     * Note: This method MUST be called before any CRUD operations are actually performed.
     * @param int $modType A numeric value representing the type of modification currently being performed. MUST equal one
     *          of the following class constants:
     *
     *              MySqlUserCrud::MOD_TYPE_DELETE // Currently not applicable, may be in the future
     *
     *              MySqlUserCrud::MOD_TYPE_READ // Currently not applicable, may be in the future
     *
     *              MySqlUserCrud::MOD_TYPE_UPDATE
     *
     *              MySqlUserCrud::MOD_TYPE_DELETE
     * @param string $targetUserName The user name of the user that the curd operation will be performed on.
     * @devNote:
     *                               This is used to read the target user's data from storage so it can
     *                               be passed on to the observer.
     * @param IUser $user The IUser implementation instance that represents the user's data. For example,
     *                    when updating a User this would be the IUser implementation that represents the
     *                    new user.
     * @devNote: observer/subject related logic
     */
    protected function setNotice(int $modType, string $targetUserName, IUser $user): void
    {
        // Set properties needed by the MySqlUserCrudObserver.
        $this->modType = $modType;
        $this->targetUser = $this->read($targetUserName); // the IUser implementation instance that represents the user's original user data.
        $this->user = $user; // the IUser implementation instance that represents the user's modified user data.
    }

    protected function packUserData(IUser $user): array
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
    final protected function packMeta(IUser $user): string
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
    final protected function packRoles(IUser $user): string
    {
        $roles = array();
        foreach ($user->getRoles() as $role) {
            array_push($roles, $role->getRoleName());
        }
        return json_encode($roles);
    }

    /**
     * Unpack the user's meta data.
     * @param string $packedMeta The packed meta data.
     * @return array Array of user's public and private meta data indexed by the strings 'public' and
     *               'private', respectively.
     */
    final protected function unpackMeta(string $packedMeta): array
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
    final protected function unpackRoles(string $packedRoles): array
    {
        $roles = array();
        $roleNames = json_decode($packedRoles, true);
        foreach ($roleNames as $roleName) {
            array_push($roles, $this->roleCrud->read($roleName));
        }
        return $roles;
    }

}
