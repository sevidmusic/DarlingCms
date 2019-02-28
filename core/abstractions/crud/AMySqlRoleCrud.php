<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-27
 * Time: 21:13
 */

namespace DarlingCms\abstractions\crud;


use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\classes\observer\crud\MySqlRoleCrudObserver;
use DarlingCms\interfaces\crud\IRoleCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\classes\database\SQL\MySqlQuery;

/**
 * Class AMySqlRoleCrud. Defines an abstract implementation of the AObservableMySqlQueryCrud abstract class
 * that can be used as a base class for AMySqlRoleCrud implementations that perform CRUD operations on
 * role data in a database via a MySqlQuery instance. Instances of this class are observed by an instance
 * of the MySqlRoleCrudObserver class which is responsible for performing updates to any users that
 * would be affected by modifications of the relevant role.
 * @package DarlingCms\abstractions\crud
 */
abstract class AMySqlRoleCrud extends AObservableMySqlQueryCrud implements IRoleCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ROLES_TABLE_NAME = 'roles';

    /**
     * @var AMySqlPermissionCrud AMySqlPermissionCrud implementation instance used to perform CRUD operations
     *                           on permissions.
     */
    protected $permissionCrud;

    /**
     * @var APDOCompatibleRole APDOCompatibleRole implementation instance that represents the role
     *                         as it was prior to modification, i.e., the original role.
     */
    public $originalRole;

    /**
     * @var APDOCompatibleRole APDOCompatibleRole implementation instance that represents the role
     *                         after modification, i.e., the modified role.
     */
    public $modifiedRole;

    /**
     * AMySqlRoleCrud constructor. Calls parent's __construct() method, passing an instance of a MySqlQuery
     * implementation, and, if the $observe parameter is set to true, an instance of a MySqlRoleCrudObserver
     * implementation. Injects the AMySqlPermissionCrud implementation instance used to perform CRUD operations
     * or permissions.
     * @param MySqlQuery $MySqlQuery Instance of a MySqlQuery implementation that will be used to connect to the
     *                               database where permissions are stored.
     * @param AMySqlPermissionCrud $permissionCrud AMySqlPermissionCrud implementation instance that will be used
     *                                             to perform CRUD operations on permission data.
     * @param bool $observe Determines whether or not this instance should be observed, if set to true then
     *                      an instance of the MySqlRoleCrudObserver class will be attached, if set to false
     *                      this instance will not be observed by any observer.
     */
    public function __construct(MySqlQuery $MySqlQuery, AMySqlPermissionCrud $permissionCrud, bool $observe = true)
    {
        switch ($observe) {
            case  true:
                parent::__construct($MySqlQuery, self::ROLES_TABLE_NAME, new MySqlRoleCrudObserver());
                break;
            case false:
                parent::__construct($MySqlQuery, self::ROLES_TABLE_NAME);
                break;
        }
        $this->permissionCrud = $permissionCrud;
    }

    /**
     * Creates the roles table.
     * @return bool True if the roles table was created, false otherwise.
     */
    protected function generateTable(): bool
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
     * Creates an array of IPermission implementation names based on the IPermission implementations
     * assigned to the role, and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         'PermissionName1',
     *         'PermissionName2',
     *         'PermissionName3',
     *     ]
     * }
     * @param IRole $role The IRole implementation whose permissions are to be packed.
     * @return string The packed permission names, i.e., the json string representing the data.
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
     * Unpack the role's permissions.
     * @param string $packedPermissions The packed permissions, i.e., the json string representing the data.
     * @return array An array of the IPermission implementation instances assigned to the role.
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
     * @param string $roleName The name of the role to check for.
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
     * @param string $roleName The name of the role.
     * @return string The fully qualified namespaced classname of the specified role.
     */
    protected function getClassName(string $roleName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IRoleType FROM roles WHERE roleName=? LIMIT 1', [$roleName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IRoleType'];
    }

    abstract public function create(IRole $role): bool;

    abstract public function read(string $roleName): IRole;

    abstract public function readAll(): array;

    abstract public function update(string $roleName, IRole $newRole): bool;

    abstract public function delete(string $roleName): bool;


}
