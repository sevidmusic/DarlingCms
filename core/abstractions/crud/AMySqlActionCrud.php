<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-21
 * Time: 00:19
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\abstractions\privilege\APDOCompatibleAction;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\privilege\IAction;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\classes\observer\crud\MySqlActionCrudObserver;
use PDO;

/**
 * Class AMySqlActionCrud. Defines an abstract implementation of the
 * AObservableMySqlQueryCrud abstract class, that also implements the
 * IActionCrud interface, which can be used as a base class for
 * IActionCrud implementations that perform CRUD operations on IAction
 * instance data in a database via a MySqlQuery instance.
 *
 * Implementations of this class can also be observed by an instance of the
 * MySqlActionCrudObserver class which is responsible for performing
 * updates to any permissions that would be affected by modifications
 * of the relevant action. To enable this, set the __construct() method's
 * $observe parameter to true on instantiation, note, the __construct()
 * method's $observe parameter is set to true by default to insure that
 * the larger permissions system remains in tact when actions are modified,
 * as permissions are defined by the actions that are assigned to them. To
 * disable observation, the __construct() method's $observe parameter must
 * be explicitly set to true.
 *
 * Note: By default observation is turned on, to turn observation off
 *       simply set the __construct() method's $observe parameter to false.
 *
 * Note: If the __construct() method's observe property is set to true, which
 *       is the default, an instance of a MySqlActionCrudObserver will be injected
 *       internally by the __construct() method on instantiation. This instance of
 *       a MySqlActionCrudObserver will be used to notify any permissions that would
 *       be effected by the modification of a specific action.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see AMySqlActionCrud::ACTIONS_TABLE_NAME
 * @see AMySqlActionCrud::__construct()
 * @see AMySqlActionCrud::generateTable()
 * @see AMySqlActionCrud::actionExists()
 * @see AMySqlActionCrud::getClassName()
 * @see AMySqlActionCrud::create()
 * @see AMySqlActionCrud::read()
 * @see AMySqlActionCrud::readAll()
 * @see AMySqlActionCrud::update()
 * @see AMySqlActionCrud::delete()
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
     * MySqlActionCrud constructor. Injects the MySqlQuery implementation
     * instance used to connect to and query the database, and determines
     * whether or not this instance should be observed by an internally
     * injected instance of the MySqlActionCrudObserver() class.
     *
     * @param MySqlObjectQuery $MySqlObjectQuery The MySqlQuery implementation instance
     *                                     used to connect to and query the database.
     *
     * @param bool $observe Determines whether or not this instance should be
     *                      observed by an internally injected instance of the
     *                      MySqlActionCrudObserver() class.
     *                      Note: By default observation is turned on, to turn
     *                            observation off simply set the $observe parameter
     *                            to false.
     *
     *                      Note: If the __construct() method's observe property is
     *                            set to true, which is the default, an instance of
     *                            a MySqlActionCrudObserver will be injected internally
     *                            by the __construct() method on instantiation. This
     *                            instance of a MySqlActionCrudObserver will be used
     *                            to notify any permissions that would be effected by
     *                            the modification of a specific action.
     *
     * @see AObservableMySqlQueryCrud::__construct()
     */
    public function __construct(MySqlObjectQuery $MySqlObjectQuery, $observe = true)
    {
        /**
         * @devNote IMPORTANT! DO NOT DELETE THE FOLLOWING DEV NOTE!
         * @devNote Allowing observation to be turned off prevents infinite
         *          loop when this class is used in certain contexts.
         *          For example the MySqlPermission crud uses this class, and the
         *          MySqlActionCrudObserver uses a MySqlPermission, so the
         *          MySqlActionCrudObserver's permission crud needs to be able to
         *          turn off observation or else the following instantiation loop
         *          will occur:
         *
         *          new actionCrud --->
         *              new MySqlActionCrudObserver --->
         *                  new permissionCrud --->
         *                          new actionCrud --->
         *                              new MySqlActionCrudObserver --->
         *                                  new permission crud --->
         *                                          new actionCrud ---->
         *                                              new MySqlActionCrudObserver --->
         *                                                  new permission crud --->
         *                                                      etc...
         */
        switch ($observe) {
            case false:
                parent::__construct($MySqlObjectQuery, self::ACTIONS_TABLE_NAME);
                break;
            default:
                parent::__construct($MySqlObjectQuery, self::ACTIONS_TABLE_NAME, new MySqlActionCrudObserver());
                break;

        }
    }

    /**
     * Creates a table named using the value of the $tableName property.
     *
     * Note: This method is intended to be called by the __construct()
     *       method on instantiation.
     *
     * NOTE: Implementations MUST implement this method in order to insure
     *       the __construct() method can create the table used by the
     *       implementation if it does not already exist in the database.
     *
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
     *
     * @param string $actionName The name of the action to check for.
     *
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
     *
     * @param string $actionName The name of the action.
     *
     * @return string The fully qualified namespaced classname of the specified action.
     */
    protected function getClassName(string $actionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IActionType FROM actions WHERE actionName=? LIMIT 1', [$actionName])->fetchAll(PDO::FETCH_ASSOC)[0]['IActionType'];
    }

    /**
     * Create a new action.
     * @param IAction $action The IAction implementation instance that
     *                        represents the action to create.
     * @return bool True if action was created, false otherwise
     */
    abstract public function create(IAction $action): bool;

    /**
     * Read a specified action from the database.
     *
     * @param string $actionName The name of the action to read.
     *
     * @return IAction An IAction implementation instance that represents the action,
     *                 or a default IAction implementation instance.
     */
    abstract public function read(string $actionName): IAction;

    /**
     * Returns an array of IAction implementation instances for each
     * action stored in the database.
     *
     * @return array An array of IAction implementation instances for each
     * action stored in the database.
     */
    abstract public function readAll(): array;

    /**
     * Update a specified action.
     *
     * @param string $actionName The action to update.
     *
     * @param IAction $newAction An IAction implementation instance that represents the
     *                           updated action.
     *
     * @return bool True if action was updated successfully, false otherwise.
     */
    abstract public function update(string $actionName, IAction $newAction): bool;

    /**
     * Deletes the specified action.
     *
     * @param string $actionName The name of the action to delete.
     *
     * @return bool True if action was deleted, false otherwise.
     */
    abstract public function delete(string $actionName): bool;

}
