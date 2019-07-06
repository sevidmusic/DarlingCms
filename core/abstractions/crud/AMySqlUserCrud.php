<?php
/**
 * Created by Sevi Darling
 * Date: 2019-02-08
 * Time: 00:44
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\abstractions\user\APDOCompatibleUser;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\classes\observer\crud\MySqlUserCrudObserver;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use DarlingCms\interfaces\crud\IUserCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;
use PDO;
use SplSubject;

/**
 * Class AMySqlUserCrud. Defines an abstract implementation of the
 * AObservableMySqlObjectQueryCrud abstract class that implements
 * the IUserCrud, SplSubject, ISqlQueryCrud, and ISqlObjectQueryCrud
 * interfaces which can be used as a base class for IUserCrud
 * implementations that perform CRUD operations on IUser instance
 * data in a database via a MySqlObjectQuery implementation instance.
 *
 * Note: Implementations of this class will always be observed by a
 *       MySqlUserCrudObserver implementation instance which is
 *       responsible for maintaining the connection between users
 *       and their respective passwords.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see AMySqlUserCrud::create()
 * @see AMySqlUserCrud::read()
 * @see AMySqlUserCrud::readAll()
 * @see AMySqlUserCrud::update()
 * @see AMySqlUserCrud::delete()
 * @see AMySqlUserCrud::generateTable()
 *
 */
abstract class AMySqlUserCrud extends AObservableMySqlObjectQueryCrud implements IUserCrud, SplSubject, ISqlQueryCrud, ISqlObjectQueryCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const USER_TABLE_NAME = 'users';

    /**
     * @var APDOCompatibleUser The APDOCompatibleUser implementation instance that
     *                         represents the target user.
     *
     * @todo Should be protected, implement getter setter methods to handle this
     *       property's value.
     */
    public $targetUser;

    /**
     * @var IUser The IUser implementation instance that represents the user's data.
     */
    public $user;

    /**
     * @var IRoleCrud Injected IRoleCrud implementation instance used to perform
     *                perform CRUD operations IRole implementation instance data.
     */
    private $roleCrud;

    /**
     * AMySqlQueryCrud constructor. Injects the MySqlQuery instance used to perform
     * CRUD operations on IUser implementation instance data, as well as the IRoleCrud
     * implementation instance used to perform CRUD operations on IRole implementation
     * instance data.
     *
     * Note: Instances of implementations of this class will always be observed by a
     *       MySqlUserCrudObserver implementation instance which will be instantiated
     *       and passed to the parent AObservableMySqlObjectQueryCrud's __construct()
     *       method on instantiation.
     *
     * @param MySqlObjectQuery $mySqlObjectQuery The MySqlQuery instance used to perform
     *                                           CRUD operations on IUser implementation
     *                                           instance data.
     *
     * @param IRoleCrud $roleCrud The IRoleCrud implementation instance used to perform
     *                            CRUD operations on IRole implementation instance data.
     */
    public function __construct(MySqlObjectQuery $mySqlObjectQuery, IRoleCrud $roleCrud)
    {
        parent::__construct($mySqlObjectQuery, self::USER_TABLE_NAME, new MySqlUserCrudObserver());
        $this->roleCrud = $roleCrud;
    }

    /**
     * Create a new user.
     *
     * @param IUser $user The IUser implementation instance that represents the user.
     *
     * @return bool True if user was created, false otherwise.
     */
    abstract public function create(IUser $user): bool;

    /**
     * Read a specified user's data.
     *
     * @param string $userName The user's username.
     *
     * @return IUser The IUser implementation instance that represents the user.
     */
    abstract public function read(string $userName): IUser;


    /**
     * Return all stored Users.
     *
     * @return array|IUser[] Array of all stored users.
     */
    abstract public function readAll(): array;

    /**
     * Update a specified user's data.
     *
     * @param string $userName The user's username.
     *
     * @param IUser $newUser The IUser implementation instance that represents
     *                       the updated user.
     *
     * @return bool True if user was update, false otherwise.
     */
    abstract public function update(string $userName, IUser $newUser): bool;

    /**
     * Delete a specified user's data.
     *
     * @param string $userName The user's username.
     *
     * @return bool True if user was deleted, false otherwise.
     */
    abstract public function delete(string $userName): bool;

    /**
     * Creates the users table.
     *
     * @return bool True if users table was created, false otherwise.
     */
    public function generateTable(): bool
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
     *
     * @param string $userName The user to check for.
     *
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
     * Get the class name including the fully qualified namespace of the specified
     * user in the following format:
     *
     * \\Some\\Name\\Spaced\\ClassName
     *
     * @param string $userName The name of the user who's fully qualified namespaced
     *                          class name should be returned.
     *
     * @return string The specified user's fully qualified namespaced class name.
     */
    final protected function getClassName(string $userName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IUserType FROM ' . $this->tableName . ' WHERE userName=? LIMIT 1', [$userName])->fetchAll(PDO::FETCH_ASSOC)[0]['IUserType'];
    }

    /**
     * Set the values of the properties that are used by observers of this subject.
     *
     * Note: This method MUST be called before any CRUD operations are actually
     *       performed.
     *
     * @param int $modType A numeric value representing the type of modification
     *                     currently being performed. MUST equal one of the following
     *                     class constants:
     *
     *                         MySqlUserCrud::MOD_TYPE_DELETE
     *
     *                         MySqlUserCrud::MOD_TYPE_READ
     *
     *                         MySqlUserCrud::MOD_TYPE_UPDATE
     *
     *                         MySqlUserCrud::MOD_TYPE_DELETE
     *
     * @param string $targetUserName The user name of the user that the crud operation
     *                               will be performed on.
     *
     * @devNote: This method is used to read the target user's data from storage so it
     *           can be passed on to the observer.
     *
     * @param IUser $user The IUser implementation instance that represents the user's
     *                    data. For example, when updating a user this would be the
     *                    IUser implementation that represents the new user.
     */
    protected function setNotice(int $modType, string $targetUserName, IUser $user): void
    {
        /**
         * Set properties needed by the MySqlUserCrudObserver.
         */
        $this->modType = $modType;
        /**
         * Set the IUser implementation instance that represents the user's
         * original user data.
         */
        $this->targetUser = $this->read($targetUserName);
        /**
         * Set the IUser implementation instance that represents the user's
         * modified user data.
         */
        $this->user = $user;
    }

    /**
     * Pack an IUser implementation instance for storage.
     *
     * @param IUser $user The IUser implementation instance to pack for storage.
     *
     * @return array The array of the IUser implementation instance's packed data.
     */
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
     * generated array as json.
     *
     * The array is constructed as follows:
     *
     *     array(
     *
     *         'public' => $user->getPublicMeta(),
     *
     *         'private' => $user->getPrivateMeta()
     *
     *     )
     *
     * Note: This array will be encoded as json.
     *
     * @param IUser $user The user whose meta data is to be packed.
     *
     * @return string The json string that represents the array of packed meta data.
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
     *
     * For Example:
     *     {
     *         [
     *             'SomeRole', // when this user is read, the read method will use the IRoleCrud implementation to look for a role name SomeRole in the database, if it finds such a role it will be assigned as one of the user's roles via the $ctor_args array.
     *             'SomeOtherRole',
     *             'AFinalRole',
     *         ]
     *     }
     *
     * @param IUser $user The user whose roles should be packed.
     *
     * @return string The json string that represents the array of the names of the
     *                roles assigned to the user..
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
     * Unpack user meta data that was encoded as a json array via the packMeta()
     * method.
     *
     * Note: This method can be used, for instance, to assign appropriate packed
     *       user meta data to a user when reading a user from the database.
     *
     * @param string $packedMeta The json string that represents the array of packed
     *                           user meta data.
     *
     * @return array Array of public and private user meta data indexed by the
     *               strings 'public' and 'private', respectively.
     */
    final protected function unpackMeta(string $packedMeta): array
    {
        return json_decode($packedMeta, true);
    }

    /**
     * Unpack a json encoded array of IRole implementations names, and return
     * an array of IRole implementation instances read from the database whose
     * names correspond to the names in the unpacked array of IRole implementation
     * names.
     *
     * Note: This method uses the injected IRoleCrud implementation to read and assign
     *       the IRole implementations named in the $packedRoles array.
     *
     * Note: Any roles named in the unpacked array that cannot be read will
     *       be represented by a default IRole implementation instance defined
     *       by the injected IRoleCrud implementation instance's read() method.
     *
     * @param string $packedRoles The packed roles.
     *
     * @return array|IRole[] An array of the IRole implementation instances read
     *                       from the database whose names correspond to the names
     *                       in the unpacked array of IRole implementation names.
     *
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
