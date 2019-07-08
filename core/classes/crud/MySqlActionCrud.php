<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-28
 * Time: 05:12
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlActionCrud;
use DarlingCms\classes\privilege\Action;
use DarlingCms\classes\staticClasses\utility\StringUtility;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use DarlingCms\interfaces\privilege\IAction;
use PDO;
use SplSubject;

/**
 * Class MySqlActionCrud. Defines an implementation of the IActionCrud, SplSubject,
 * ISqlQueryCrud, ISqlObjectQueryCrud interfaces that extends the AMySqlActionCrud
 * abstract class. This implementation can be used to perform CRUD operations on
 * IAction implementation instance data in a MySql database.
 *
 * @package DarlingCms\classes\crud
 */
class MySqlActionCrud extends AMySqlActionCrud implements IActionCrud, SplSubject, ISqlQueryCrud, ISqlObjectQueryCrud
{
    /**
     * Create a new action.
     *
     * @param IAction $action The IAction implementation instance that represents
     *                        the action.
     *
     * @return bool True if action was created, false otherwise.
     */
    public function create(IAction $action): bool
    {
        // remove bad characters, preserve whitespace
        $formattedActionName = StringUtility::filterAlphaNumeric($action->getActionName(), true);
        // make sure a action with same name does not already exist.
        if ($this->actionExists($formattedActionName) === true) {
            return false;
        }
        // create user
        $this->MySqlQuery->executeQuery('INSERT INTO ' . $this->tableName .
            ' (actionName, actionDescription, IActionType) VALUES (?,?,?)',
            [
                $formattedActionName,
                $action->getActionDescription(),
                $this->formatClassName(get_class($action))
            ]
        );
        return $this->actionExists($formattedActionName);
    }

    /**
     * Read the specified action's data from the database and return an appropriate
     * IAction implementation instance.
     *
     * @param string $actionName The name of the action read.
     *
     * @return IAction The IAction implementation instance that represents
     *                 the specified action, or, if the specified action does
     *                 not exist this method will return a default IAction
     *                 instance that defines an action named "No Access".
     */
    public function read(string $actionName): IAction
    {
        if ($this->actionExists($actionName) === true) {
            // 1. get action data
            $actionData = $this->MySqlQuery->executeQuery('SELECT * FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', [$actionName])->fetchAll(PDO::FETCH_ASSOC)[0];
            // 2. create ctor_args array
            $ctor_args = array($actionData['actionName'], $actionData['actionDescription']);
            // 3. Instantiate the appropriate IAction implementation based on the action data.
            $results = $this->MySqlQuery->getClass('SELECT * FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', $this->getClassName($actionName), [$actionName], $ctor_args);
            return array_shift($results);
        }
        return new Action('No Access', 'No Access');
    }

    /**
     * Read all stored actions and return an array of IAction implementations
     * instances for all stored actions.
     *
     * @return array|IAction[] An array of IAction implementations instances
     *                         for all stored actions.
     */
    public function readAll(): array
    {
        $actionNames = $this->MySqlQuery->executeQuery('SELECT actionName FROM ' . $this->tableName . ' ORDER BY actionName ASC')->fetchAll(PDO::FETCH_ASSOC);
        $actions = array();
        foreach ($actionNames as $actionName) {
            array_push($actions, $this->read($actionName['actionName']));
        }
        return $actions;
    }

    /**
     * Update the specified action.
     *
     * @param string $actionName The name of the action to update.
     *
     * @param IAction $newAction The IAction implementation instance that represents
     *                           the updated action.
     *
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
     *
     * @param string $actionName The name of the action to delete.
     *
     * @return bool True if action was deleted, false otherwise.
     */
    public function delete(string $actionName): bool
    {
        if ($this->actionExists($actionName) === true) {
            /**
             * Only set observer properties if mod type is not set to update, this
             * will prevent this method from overwriting said properties if they were
             * already set by the update method.
             *
             * This precaution is necessary because the update() method uses this method
             * as part of it's logic.
             */
            if ($this->modType !== self::MOD_TYPE_UPDATE) {
                $this->modType = self::MOD_TYPE_DELETE;
                $this->originalAction = $this->read($actionName);
                /**
                 * In the following context modified and original are the same
                 * since the action is being deleted.
                 */
                $this->modifiedAction = $this->originalAction;
            }
            // Delete the action
            $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', [$actionName]);
            // If action was deleted successfully notify the observer and return true.
            if ($this->actionExists($actionName) === false) {
                /**
                 * Only notify observer of action deletion if mod type is set to delete,
                 * if mod type is update DO NOT notify observer here, the update() method
                 * will notify the observer if appropriate.
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
