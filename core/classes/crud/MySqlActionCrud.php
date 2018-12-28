<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-28
 * Time: 05:12
 */

namespace DarlingCms\classes\crud;


use DarlingCms\abstractions\crud\AMySqlQueryCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\privilege\Action;
use DarlingCms\interfaces\crud\IActionCrud;
use DarlingCms\interfaces\privilege\IAction;

/**
 * Class MySqlActionCrud. Defines an implementation of the IActionCrud interface
 * that extends the AMySqlActionCrud abstract class. This implementation can be used
 * to perform CRUD operations on action data in a MySql database.
 * @package DarlingCms\classes\crud
 */
class MySqlActionCrud extends AMySqlQueryCrud implements IActionCrud
{
    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ACTIONS_TABLE_NAME = 'actions';

    /**
     * MySqlActionCrud constructor. Injects the MySqlQuery instance used for CRUD operations on action data.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     */
    public function __construct(MySqlQuery $MySqlQuery)
    {
        parent::__construct($MySqlQuery, self::ACTIONS_TABLE_NAME);
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
     * @return IAction The IAction implementation instance. If the specified action does not exit this
     *                 method will return a default IAction instance that defines an action called no access.
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
        return new Action('No Access', 'No Access'); // @todo Implement default Action, i.e., class NoAccessAction()...
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
            if ($this->delete($actionName) === true) { // @devNote, if you start getting duplication errors add the following condition: && $actionName === $newAction->getActionName()
                return $this->create($newAction);
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
        $this->MySqlQuery->executeQuery('DELETE FROM ' . $this->tableName . ' WHERE actionName=? LIMIT 1', [$actionName]);
        return $this->actionExists($actionName) === false;
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
            actionName VARCHAR(256) NOT NULL UNIQUE,
            actionDescription VARCHAR(256) NOT NULL UNIQUE,
            IActionType VARCHAR(256) NOT NULL
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
    private function actionExists(string $actionName): bool
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
    private function getClassName(string $actionName): string
    {
        return $this->MySqlQuery->executeQuery('SELECT IActionType FROM actions WHERE actionName=? LIMIT 1', [$actionName])->fetchAll(\PDO::FETCH_ASSOC)[0]['IActionType'];
    }
}
