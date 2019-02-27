<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-25
 * Time: 08:12
 */

namespace DarlingCms\abstractions\crud;


use DarlingCms\classes\observer\crud\MySqlPermissionCrudObserver;
use DarlingCms\interfaces\crud\IPermissionCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\privilege\IPermission;

/**
 * Class AMySqlPermissionCrud. Defines an abstract implementation of the AObservableMySqlQueryCrud abstract class
 * that can be used as a base class for AMySqlPermissionCrud implementations that perform CRUD
 * operations on permission data in a database via a MySqlQuery instance. Instances of this class are observed
 * by an instance of the MySqlPermissionCrudObserver class which is responsible for performing updates to
 * any roles that would be affected by modifications of the relevant permission.
 * @package DarlingCms\abstractions\crud
 */
abstract class AMySqlPermissionCrud extends AObservableMySqlQueryCrud implements IPermissionCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const PERMISSIONS_TABLE_NAME = 'permissions';

    /**
     * @var IActionCrud Injected instance of an IActionCrud implementation.
     */
    protected $actionCrud;

    public $originalPermission;

    public $modifiedPermission;

    /**
     * AMySqlPermissionCrud constructor. Injects the MySqlQuery instance used for CRUD operations on
     * permission data. Injects the IActionCrud implementation instance used for CRUD operations on
     * action data.
     * @param MySqlQuery $mySqlQuery
     * @param IActionCrud $actionCrud
     */
    public function __construct(MySqlQuery $mySqlQuery, IActionCrud $actionCrud, bool $observe = true)
    {
        switch ($observe) {
            case true:
                parent::__construct($mySqlQuery, self::PERMISSIONS_TABLE_NAME, new MySqlPermissionCrudObserver());
                break;
            case false;
                parent::__construct($mySqlQuery, self::PERMISSIONS_TABLE_NAME);
                break;
        }
        $this->actionCrud = $actionCrud;
    }

    /**
     * Creates the permissions table.
     * @return bool True if permissions table was created, false otherwise.
     */
    protected function generateTable(): bool
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
     * Determine whether or not a specified permission exists.
     * @param string $permissionName The name of the permission to check for.
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
     * @param string $permissionName The name of the permission.
     * @return string The fully qualified namespaced classname of the specified permission.
     */
    protected function getClassName(string $permissionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IPermissionType FROM permissions WHERE permissionName=? LIMIT 1', [$permissionName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IPermissionType'];
    }

    /**
     * Creates an array of IAction implementation names based on the IAction implementations
     * assigned to the IPermission implementation instance, and encodes the generated array as json.
     * For Example:
     * {
     *     [
     *         'ActionName1',
     *         'ActionName2',
     *         'ActionName3',
     *     ]
     * }
     * @param IPermission $Permission The IPermission implementation instance whose Permissions are to be packed.
     * @return string The packed permission names, i.e., the json string representing the data.
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
     * Unpack the Permission's permissions.
     * @param string $packedPermissions The packed permissions, i.e., the json string representing the data.
     * @return array An array of the IPermission implementation instances assigned to the Permission.
     */
    final protected function unpackActions(string $packedPermissions): array
    {
        $unpackedData = json_decode($packedPermissions);
        $permissions = [];
        foreach ($unpackedData as $actionName) {
            array_push($permissions, $this->actionCrud->read($actionName));
        }
        return $permissions;
    }

    abstract public function create(IPermission $permission): bool;

    abstract public function read(string $permissionName): IPermission;

    /**
     * @param string $permissionName
     * @return array|IPermission[]
     */
    abstract public function readAll(): array;

    abstract public function update(string $permissionName, IPermission $newPermission): bool;

    abstract public function delete(string $permissionName): bool;


}
