<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-21
 * Time: 00:19
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\abstractions\privilege\APDOCompatibleAction;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\privilege\IAction;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\observer\crud\MySqlActionCrudObserver;

/**
 * Class AMySqlActionCrud. Defines an abstract implementation of the AObservableMySqlQueryCrud abstract class
 * that can be used as a base class for AMySqlActionCrud implementations that perform CRUD
 * operations on action data in a database via a MySqlQuery instance. Instances of this class are observed
 * by an instance of the MySqlActionCrudObserver class which is responsible for performing updates to
 * any permissions that would be affected by modifications of the relevant action.
 * @package DarlingCms\abstractions\crud
 */
abstract class AMySqlActionCrud extends AObservableMySqlQueryCrud implements IActionCrud
{

    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ACTIONS_TABLE_NAME = 'actions';

    /**
     * @var APDOCompatibleAction The original action.
     */
    public $originalAction;

    /**
     * @var APDOCompatibleAction The modified action.
     */
    public $modifiedAction;

    /**
     * MySqlActionCrud constructor.
     * @param MySqlQuery $MySqlQuery The MySqlQuery implementation instance used to connect to and
     *                               query the database.
     * @param bool $observe Determines whether or not this instance should be observable.
     */
    public function __construct(MySqlQuery $MySqlQuery, $observe = true)
    {
        switch ($observe) {
            case false: // this prevents infinite loop when this class is used or injected by other classes, for example the MySqlPermission crud uses this class, and the MySqlActionCrudObserver uses a permission crud, so the MySqlActionCrudObserver's permission crud needs to be able to turn off observation for it's instance  or else  the following instantiation loop will occur: new actionCrud ---> new MySqlActionCrudObserver ---> new permissionCrud ---> new actionCrud ---> new MySqlActionCrudObserver ---> new permission crud ---> new actionCrud ----> new MySqlActionCrudObserver ---> etc.
                parent::__construct($MySqlQuery, self::ACTIONS_TABLE_NAME);
                break;
            default:
                parent::__construct($MySqlQuery, self::ACTIONS_TABLE_NAME, new MySqlActionCrudObserver());
                break;

        }
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
            actionName VARCHAR(242) NOT NULL UNIQUE,
            actionDescription VARCHAR(242) NOT NULL UNIQUE,
            IActionType VARCHAR(242) NOT NULL
        );') === false) {
            error_log('Action Crud Error: Failed to create ' . $this->tableName . ' table');
        }
        return $this->tableExists($this->tableName);
    }

    /**
     * Determine whether or not a specified action exists.
     * @param string $actionName The name of the action to check for.
     * @return bool True if action exists, false otherwise.
     */
    protected function actionExists(string $actionName): bool
    {
        $actionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE actionName=?', [$actionName])->fetchAll();
        if (empty($actionData) === true) {
            return false;
        }
        return true;
    }

    /**
     * Get the fully qualified namespaced classname of the specified action.
     * @param string $actionName The name of the action.
     * @return string The fully qualified namespaced classname of the specified action.
     */
    protected function getClassName(string $actionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IActionType FROM actions WHERE actionName=? LIMIT 1', [$actionName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IActionType'];
    }

    abstract public function create(IAction $action): bool;

    abstract public function read(string $actionName): IAction;

    abstract public function readAll(): array;

    abstract public function update(string $actionName, IAction $newAction): bool;

    abstract public function delete(string $actionName): bool;

}
