<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-28
 * Time: 05:12
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlActionCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\observer\crud\MySqlActionCrudObserver;
use DarlingCms\classes\privilege\Action;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\privilege\IAction;

/**
 * Class MySqlActionCrud. Defines an implementation of the IActionCrud interface
 * that extends the AMySqlActionCrud abstract class. This implementation can be used
 * to perform CRUD operations on action data in a MySql database.
 * @package DarlingCms\classes\crud
 */
class MySqlActionCrud extends AMySqlActionCrud implements IActionCrud
{
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
     * Create a new action.
     * @param IAction $action The IAction implementation instance that represents the action.
     * @return bool True if action was created, false otherwise.
     */
    public function create(IAction $action): bool
    {
        // make sure a action with same name does not already exist.
        if ($this->actionExists($action->getActionName()) === true) {
            return false;
        }
        // create user
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (actionName, actionDescription, IActionType) VALUES (?,?,?)',
            [
                $action->getActionName(),
                $action->getActionDescription(),
                $this->formatClassName(get_class($action))
            ]
        );
        return $this->actionExists($action->getActionName());
    }

    /**
     * Read the specified action's data from the database and return an appropriate IAction implementation
     * instance.
     * @param string $actionName The name of the action to return an IAction implementation instance for.
     * @return IAction The IAction implementation instance. If the specified action does not exist this
     *                 method will return a default IAction instance that defines an action named "No Access".
     */
    public function read(string $actionName): IAction
    {
        if ($this->actionExists($actionName) === true) {
            // 1. get action data
            $actionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', [$actionName])->fetchAll(\PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($actionData['actionName'], $actionData['actionDescription']);
            // 3. Instantiate the appropriate IAction implementation based on the action data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', $this->getClassName($actionName), [$actionName], $ctor_args);
            return array_shift($results);
        }
        return new Action('No Access', 'No Access');
    }

    /**
     * Read all stored actions into an array.
     * @return array|IAction[] An array of all stored IAction implementations.
     */
    public function readAll(): array
    {
        $actionNames = $this->MySqlQuery->executeQuery('SELECT actionName FROM ' . $this->tableName)->fetchAll(\PDO::FETCH_ASSOC);
        $actions = array();
        foreach ($actionNames as $actionName) {
            array_push($actions, $this->read($actionName['actionName']));
        }
        return $actions;
    }

    /**
     * Update the specified action.
     * @param string $actionName The name of the action to update.
     * @param IAction $newAction The IAction implementation instance that represents the new action.
     * @return bool True if action was updated, false otherwise.
     */
    public function update(string $actionName, IAction $newAction): bool
    {
        if ($this->actionExists($actionName) === true) {
            $this->modType = self::MOD_TYPE_UPDATE;
            $this->originalAction = $this->read($actionName);
            $this->modifiedAction = $newAction;
            if ($this->delete($actionName) === true && $this->create($newAction) === true) {
                $this->notify();
                return true;
            }
        }
        return false;
    }

    /**
     * Delete the specified action.
     * @param string $actionName The name of the action to delete.
     * @return bool True if action was deleted, false otherwise.
     */
    public function delete(string $actionName): bool
    {
        if ($this->actionExists($actionName) === true) {
            /**
             * Only set observer properties if mod type is not set to update, this will prevent this
             * method from overwriting said properties if they were already set by the update method.
             * This precaution is necessary because the update() method uses this method as part of
             * it's logic.
             */
            if ($this->modType !== self::MOD_TYPE_UPDATE) {
                $this->modType = self::MOD_TYPE_DELETE;
                $this->originalAction = $this->read($actionName);
                $this->modifiedAction = $this->originalAction; // in this context modified and original are the same since the action is being deleted.
            }
            // Delete the action
            $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', [$actionName]);
            // If action was deleted successfully notify the observer and return true.
            if ($this->actionExists($actionName) === false) {
                /**
                 * Only notify observer of action deletion if mod type is set to delete, if mod type is
                 * update DO NOT notify observer here, the update() method will notify the observer if
                 * appropriate.
                 */
                if ($this->modType === self::MOD_TYPE_DELETE) {
                    $this->notify();
                }
                return true;
            }
        }
        return false;
    }

}
