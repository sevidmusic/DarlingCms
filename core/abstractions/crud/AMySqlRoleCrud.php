<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-27
 * Time: 21:13
 */

namespace DarlingCms\abstractions\crud;


use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\classes\observer\crud\MySqlRoleCrudObserver;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use PDO;
use SplSubject;

/**
 * Class AMySqlRoleCrud. Defines an abstract implementation of the
 * AObservableMySqlObjectQueryCrud abstract class that implements
 * the IRoleCrud, SplSubject, ISqlQueryCrud, and ISqlObjectQueryCrud
 * interfaces which can be used as a base class for IRoleCrud
 * implementations that perform CRUD operations on IRole instance
 * data in a database via a MySqlObjectQuery implementation instance.
 *
 * Implementations of this class can be observed by an internally injected
 * instance of the MySqlRoleCrudObserver class which is responsible for
 * performing updates to any users that would be affected by modifications
 * of the relevant role. To enable this, set the __construct() method's $observe
 * parameter to true on instantiation.
 *
 * Note: The __construct() method's $observe parameter is set to true by default
 *       to insure that the larger users system remains in tact when roles
 *       are modified, as users are defined by the roles that are assigned
 *       to them. To disable observation, the __construct() method's $observe
 *       parameter must be explicitly set to false.
 *
 * Note: By default observation is turned on, to turn observation off
 *       simply set the __construct() method's $observe parameter to false.
 *
 * Note: If the __construct() method's $observe property is set to true, which
 *       is the default, an instance of a MySqlRoleCrudObserver will be injected
 *       internally by the __construct() method on instantiation. This instance of
 *       a MySqlRoleCrudObserver will be used to notify any users that would
 *       be effected by the modification of a specific role.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see IRoleCrud
 * @see SplSubject
 * @see ISqlQueryCrud
 * @see ISqlObjectQueryCrud
 * @see AObservableMySqlObjectQueryCrud
 * @see MySqlRoleCrudObserver
 * @see AMySqlRoleCrud
 * @see AMySqlRoleCrud::ROLES_TABLE_NAME
 * @see AMySqlRoleCrud::generateTable()
 * @see AMySqlRoleCrud::create()
 * @see AMySqlRoleCrud::read()
 * @see AMySqlRoleCrud::readAll()
 * @see AMySqlRoleCrud::update()
 * @see AMySqlRoleCrud::delete()
 */
abstract class AMySqlRoleCrud extends AObservableMySqlObjectQueryCrud implements IRoleCrud, SplSubject, ISqlQueryCrud, ISqlObjectQueryCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ROLES_TABLE_NAME = 'roles';

    /**
     * @var AMySqlPermissionCrud AMySqlPermissionCrud implementation instance used
     *      to perform CRUD operations on permissions.
     */
    protected $permissionCrud;

    /**
     * @var APDOCompatibleRole APDOCompatibleRole implementation instance that
     *      represents the original role.
     * @todo: Make protected and implement setter and getter methods to enforce type, i.e. setOriginalRole(), getOriginalRole()
     */
    public $originalRole;

    /**
     * @var APDOCompatibleRole APDOCompatibleRole implementation instance that
     *                         represents the modified role.
     * @todo: Make protected and implement setter and getter methods to enforce type, i.e. setModifiedRole(), getModifiedRole()
     */
    public $modifiedRole;

    /**
     * AMySqlRoleCrud constructor. Calls parent's __construct() method, passing an
     * instance of a MySqlObjectQuery implementation instance, and, if the $observe
     * parameter is set to true, an instance of a MySqlRoleCrudObserver implementation
     * as well.
     *
     * Also injects the AMySqlPermissionCrud implementation instance used to perform
     * CRUD operations on permissions.
     *
     * @param MySqlObjectQuery $mySqlObjectQuery MySqlObjectQuery implementation instance
     *                                           that will be used to connect to and query
     *                                           the database where IRole instance data
     *                                           is stored.
     *
     * @param AMySqlPermissionCrud $permissionCrud AMySqlPermissionCrud implementation
     *                                             instance that will be used to perform
     *                                             CRUD operations on stored IPermission
     *                                             instance data.
     *
     * @param bool $observe Determines whether or not this instance should be observed,
     *                      if set to true then an instance of the MySqlRoleCrudObserver
     *                      class will be attached, if set to false this instance will
     *                      not be observed by any observer.
     */
    public function __construct(MySqlObjectQuery $mySqlObjectQuery, AMySqlPermissionCrud $permissionCrud, bool $observe = true)
    {
        switch ($observe) {
            case  true:
                parent::__construct($mySqlObjectQuery, self::ROLES_TABLE_NAME, new MySqlRoleCrudObserver());
                break;
            case false:
                parent::__construct($mySqlObjectQuery, self::ROLES_TABLE_NAME);
                break;
        }
        $this->permissionCrud = $permissionCrud;
    }

    /**
     * Creates the roles table.
     *
     * @return bool True if the roles table was created, false otherwise.
     */
    public function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            roleName VARCHAR(242) NOT NULL UNIQUE,
            rolePermissions TEXT NOT NULL,
            IRoleType VARCHAR(242) NOT NULL
        );') === false) {
            error_log('Role Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    /**
     * Creates an array of IPermission implementation names based on the IPermission
     * implementation instances assigned to the role, and encodes the generated array
     * as json.
     *
     * For Example:
     * {
     *     [
     *         'PermissionName1',
     *         'PermissionName2',
     *         'PermissionName3',
     *     ]
     * }
     *
     * @param IRole $role The IRole implementation whose permissions are to be packed.
     *
     * @return string The packed permission names, i.e., the json string representing
     *                an array of the names of the IPermission instances assigned to
     *                the role.
     */
    final protected function packPermissions(IRole $role): string
    {
        $permissions = array();
        foreach ($role->getPermissions() as $permission) {
            array_push($permissions, $permission->getPermissionName());
        }
        return json_encode($permissions);
    }

    /**
     * Unpack a json string that represents an array of IPermission instance names
     * and return an array of IPermission instances named according to the names
     * in the unpacked array of IPermission instance names.
     *
     * Note: The array returned by this method can be used, for instance, to assign
     *       appropriate permissions to a role when reading a role from the database.
     *
     * @param string $packedPermissions The json string that represents the array of
     *                                  IPermission instance names.
     *
     * @return array An array of IPermission instances named according to the
     *               names in the unpacked array of IPermission instance names.
     */
    final protected function unpackPermissions(string $packedPermissions): array
    {
        $unpackedData = json_decode($packedPermissions);
        $permissions = [];
        foreach ($unpackedData as $permissionName) {
            array_push($permissions, $this->permissionCrud->read($permissionName));
        }
        return $permissions;
    }

    /**
     * Determine whether or not a specified role exists.
     *
     * @param string $roleName The name of the role to check for.
     *
     * @return bool True if role exists, false otherwise.
     */
    protected function roleExists(string $roleName): bool
    {
        $roleData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE roleName=?', [$roleName])->fetchAll();
        if (empty($roleData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Get the fully qualified namespaced classname of the specified role.
     *
     * @param string $roleName The name of the role.
     *
     * @return string The fully qualified namespaced classname of the specified role.
     */
    protected function getClassName(string $roleName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IRoleType FROM roles WHERE roleName=? LIMIT 1', [$roleName])->fetchAll(PDO::FETCH_ASSOC)[0]['IRoleType'];
    }

    /**
     * Create a new role.
     *
     * @param IRole $role The IRole implementation instance that represents the role
     *                    being created.
     *
     * @return bool True if the role was created, false otherwise.
     */
    abstract public function create(IRole $role): bool;

    /**
     * Read the specified role from the database and return an IRole implementation
     * instance that represents the role, or if the specified role does not exists,
     * a IRole instance that represents a default role that is not assigned any
     * permissions.
     *
     * Note: Implementations MUST return a default IRole implementation instance
     *       in the event that the specified role cannot be read, this default
     *       instance MUST not have any permissions assigned to it to prevent
     *       unintended privilege escalation if specified role is cannot be
     *       read.
     *
     * @param string $roleName The name of the role to read from the database.
     *
     * @return IRole The IRole implementation instance that represents the role,
     *               or if the specified role does not exists, a IRole instance
     *               that represents a default role that is not assigned any
     *               permissions.
     */
    abstract public function read(string $roleName): IRole;

    /**
     * Returns an array of IRole implementation instances for all the roles
     * stored in the database.
     *
     * @return array An array of IRole implementation instances for all the roles
     *               stored in the database.
     */
    abstract public function readAll(): array;

    /**
     * Update the specified role.
     *
     * @param string $roleName The name of the role to update.
     *
     * @param IRole $newRole The IRole implementation instance that represents the
     *                       updated role.
     *
     * @return bool True if the role was updated, false otherwise.
     */
    abstract public function update(string $roleName, IRole $newRole): bool;

    /**
     * Delete the specified role.
     *
     * @param string $roleName The name of the role to delete.
     *
     * @return bool True if the role was deleted, false otherwise.
     */
    abstract public function delete(string $roleName): bool;


}
