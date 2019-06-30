<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-25
 * Time: 08:12
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\classes\observer\crud\MySqlPermissionCrudObserver;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use DarlingCms\interfaces\privilege\IPermission;
use PDO;
use SplSubject;

/**
 * Class AMySqlPermissionCrud. Defines an abstract implementation of the
 * AObservableMySqlObjectQueryCrud abstract class that implements the
 * IPermissionCrud, SplSubject, ISqlQueryCrud, and ISqlObjectQueryCrud
 * interfaces which can be used as a base class for IPermissionCrud
 * implementations that perform CRUD operations on IPermission instance
 * data in a database via a MySqlObjectQuery instance.
 *
 * Implementations of this class can be observed by an internally injected
 * instance of the MySqlPermissionCrudObserver class which is responsible for
 * performing updates to any roles that would be affected by modifications
 * of the relevant permission. To enable this, set the __construct() method's
 * $observe parameter to true on instantiation.
 *
 * Note: The __construct() method's $observe parameter is set to true by default
 *       to insure that the larger role system remains in tact when permissions
 *       are modified, as roles are defined by the permissions that are assigned
 *       to them. To disable observation, the __construct() method's $observe
 *       parameter must be explicitly set to false.
 *
 * Note: By default observation is turned on, to turn observation off
 *       simply set the __construct() method's $observe parameter to false.
 *
 * Note: If the __construct() method's $observe property is set to true, which
 *       is the default, an instance of a MySqlPermissionCrudObserver will be injected
 *       internally by the __construct() method on instantiation. This instance of
 *       a MySqlPermissionCrudObserver will be used to notify any roles that would
 *       be effected by the modification of a specific permission.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see IPermissionCrud
 * @see SplSubject
 * @see ISqlQueryCrud
 * @see ISqlObjectQueryCrud
 * @see AMySqlPermissionCrud
 * @see AMySqlPermissionCrud::PERMISSIONS_TABLE_NAME
 * @see AMySqlPermissionCrud::__construct()
 * @see AMySqlPermissionCrud::generateTable()
 * @see AMySqlPermissionCrud::create()
 * @see AMySqlPermissionCrud::read()
 * @see AMySqlPermissionCrud::readAll()
 * @see AMySqlPermissionCrud::update()
 * @see AMySqlPermissionCrud::delete()
 */
abstract class AMySqlPermissionCrud extends AObservableMySqlObjectQueryCrud implements IPermissionCrud, SplSubject, ISqlQueryCrud, ISqlObjectQueryCrud
{
    /**
     * @var string Name of the table IPermission instance data is stored in.
     */
    const PERMISSIONS_TABLE_NAME = 'permissions';

    /**
     * @var IActionCrud Injected instance of an IActionCrud implementation used
     *                  to read IAction instance data from the database.
     *
     * @devNote: This class MUST not perform create, read, or delete operations
     *           on IAction instance data via the IActionCrud instance assigned
     *           to this property, the IActionCrud implementation instance MUST
     *           be strictly used to read IAction instance data from the database.
     */
    protected $actionCrud;

    /**
     * @var APDOCompatiblePermission Instance of a APDOCompatiblePermission implementation
     *                               that represents the original permission.
     * @todo !  Implement setter and getter methods:
     *          setOriginalPermission(APDOCompatiblePermission $permission):bool;
     *          getOriginalPermission():APDOCompatiblePermission;
     */
    protected $originalPermission;

    /**
     * @var APDOCompatiblePermission Instance of a APDOCompatiblePermission implementation
     *                               that represents the updated permission.
     * @todo ! Implement setter and getter methods:
     *         setModifiedPermission(APDOCompatiblePermission $permission):bool;
     *         getModifiedPermission():APDOCompatiblePermission;
     */
    protected $modifiedPermission;

    /**
     * AMySqlPermissionCrud constructor. Injects the MySqlObjectQuery instance used to
     * perform CRUD operations on IPermission instance data in the database. Injects the
     * IActionCrud implementation instance used to perform CRUD operations on IAction
     * instance data in the database.
     *
     * @param MySqlObjectQuery $mySqlObjectQuery The MySqlObjectQuery instance used to
     *                                           perform CRUD operations on IPermission
     *                                           instance data in the database.
     *
     * @param IActionCrud $actionCrud The IActionCrud implementation instance used to
     *                                perform CRUD operations on IAction instance data
     *                                in the database.
     *
     * @param bool $observe Determines whether or not this instance should be
     *                      observed by an internally injected instance of the
     *                      MySqlPermissionCrudObserver class.
     *
     *                      Note: By default observation is turned on, to turn
     *                            observation off simply set the $observe parameter
     *                            to false.
     *
     *                      Note: If the __construct() method's observe property
     *                            is set to true, which is the default, an instance
     *                            of a MySqlPermissionCrudObserver will be injected
     *                            internally by the __construct() method on instantiation.
     *                            This instance of a MySqlActionCrudObserver will be
     *                            used to notify any roles that would be effected by
     *                            the modification of a specific permission.
     *
     */
    public function __construct(MySqlObjectQuery $mySqlObjectQuery, IActionCrud $actionCrud, bool $observe = true)
    {
        /**
         * Determine how to construct this instance based on whether or not
         * observation is turned on or off, i.e., whether the $observe
         * parameter was set to true or false, respectively.
         */
        switch ($observe) {
            case true:
                parent::__construct($mySqlObjectQuery, self::PERMISSIONS_TABLE_NAME, new MySqlPermissionCrudObserver());
                break;
            case false;
                parent::__construct($mySqlObjectQuery, self::PERMISSIONS_TABLE_NAME);
                break;
        }
        /**
         * Assign the injected IActionCrud implementation instance to
         * the $actionCrud property.
         */
        $this->actionCrud = $actionCrud;
    }

    /**
     * Creates the table IPermission instance data will be stored in.
     *
     * @return bool True if the table was created, false otherwise.
     */
    public function generateTable(): bool
    {
        if ($this->MySqlQuery->executeQuery('CREATE TABLE ' . $this->tableName . ' (
            tableId INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
            permissionName VARCHAR(242) NOT NULL UNIQUE,
            permissionActions TEXT NOT NULL,
            IPermissionType VARCHAR(242) NOT NULL
        );') === false) {
            error_log('Permission Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    /**
     * Determine whether or not a specified permission exists in the database.
     *
     * @param string $permissionName The name of the permission to check for.
     *
     * @return bool True if permission exists, false otherwise.
     */
    protected function permissionExists(string $permissionName): bool
    {
        $permissionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE permissionName=?', [$permissionName])->fetchAll();
        if (empty($permissionData) === true) {
            return false;
        }
        return true;
    }


    /**
     * Get the fully qualified namespaced classname of the specified permission.
     *
     * @param string $permissionName The name of the permission.
     *
     * @return string The fully qualified namespaced classname of the specified permission.
     */
    protected function getClassName(string $permissionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IPermissionType FROM permissions WHERE permissionName=? LIMIT 1', [$permissionName])->fetchAll(PDO::FETCH_ASSOC)[0]['IPermissionType'];
    }

    /**
     * Creates an array the names of the IAction implementations assigned
     * to the provided IPermission implementation instance, and encodes
     * the generated array as a json string.
     *
     * For Example, packActions(IPermission $Permission) may return a string like:
     * {
     *     [
     *         'IActionInstanceName1',
     *         'IActionInstanceName2',
     *         'IActionInstanceName3',
     *     ]
     * }
     *
     * @param IPermission $Permission The IPermission implementation instance whose
     *                                 actions are to be packed.
     *
     * @return string The json array of action names, i.e., the json string
     *                representing the array of actions names.
     */
    final protected function packActions(IPermission $Permission): string
    {
        $permissions = array();
        foreach ($Permission->getActions() as $action) {
            array_push($permissions, $action->getActionName());
        }
        return json_encode($permissions);
    }

    /**
     * Unpack the specified json encoded array of action names, and return
     * an array of IAction implementation instances whose names correlate
     * to the names in the unpacked array of action names.
     *
     * Note: This method expects the string supplied to the $packedActions
     *       parameter to be a string generated by the packActions() method,
     *       or a valid json string representing a numerically indexed array
     *       action names as strings.
     *
     * Note: Any actions that are named that do not exist will have a default
     *       IAction instance created for them, what this default instance
     *       looks like is determined by the IActionCrud implementation
     *       assigned to this instance's $actionCrud property.
     *
     * @param string $packedActions The packed actions, i.e., the json string
     *                              representing the array of the names of the
     *                              actions to unpack.
     *
     * @return array An array of IAction implementation instances whose names correlate
     *               to the names in the unpacked array of action names.
     */
    final protected function unpackActions(string $packedActions): array
    {
        $unpackedData = json_decode($packedActions);
        sort($unpackedData);
        $permissions = [];
        foreach ($unpackedData as $actionName) {
            array_push($permissions, $this->actionCrud->read($actionName));
        }
        return $permissions;
    }

    /**
     * Create a new permission.
     *
     * @param IPermission $permission The IPermission implementation instance that
     *                                represents the permission to create.
     *
     * @return bool True if permission was created, false otherwise.
     */
    abstract public function create(IPermission $permission): bool;

    /**
     * Read a specified permission from the database.
     *
     * @param string $permissionName The name of the permission to read.
     *
     * @return IPermission An IPermission implementation instance that represents
     *                     the permission, or a IPermission implementation instance
     *                     that serves as a default permission if the specified
     *                     permission does not exist.
     */
    abstract public function read(string $permissionName): IPermission;

    /**
     * Read all permissions from the database.
     *
     * @return array|IPermission[] An array of IPermission implementation instances for
     *                             each of the permissions stored in the database.
     */
    abstract public function readAll(): array;

    /**
     * Update a specified permission in the database.
     *
     * @param string $permissionName The name of the permission to update.
     *
     * @param IPermission $newPermission An IPermission implementation instance that
     *                                   represents the updated permission.
     *
     * @return bool True if permission was updated, false otherwise.
     */
    abstract public function update(string $permissionName, IPermission $newPermission): bool;

    /**
     * Deletes the specified permission.
     *
     * @param string $permissionName The name of the permission to delete.
     *
     * @return bool True if permission was deleted, false otherwise.
     */
    abstract public function delete(string $permissionName): bool;

}
