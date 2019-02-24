<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-21
 * Time: 00:19
 */

namespace DarlingCms\abstractions\crud;

/**
 * Class AMySqlActionCrud. Defines an abstract implementation of the AObservableMySqlQueryCrud abstract class
 * that can be used as a base class for AMySqlActionCrud implementations that perform CRUD
 * operations on action data in a database via a MySqlQuery instance. Instances of this class are observed
 * by an instance of the MySqlActionCrudObserver class which is responsible for performing updates to
 * any permissions that would be affected by modifications of the relevant action.
 * @package DarlingCms\abstractions\crud
 */
abstract class AMySqlActionCrud extends AObservableMySqlQueryCrud
{

    /**
     * @var string Name of the table this class performs CRUD operations on.
     */
    const ACTIONS_TABLE_NAME = 'actions';

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

}
