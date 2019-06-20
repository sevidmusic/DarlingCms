<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-20
 * Time: 19:38
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\privilege\IAction;

/**
 * Interface IActionCrud. Defines the contract of an object that is responsible for
 * creating, reading, updating, and deleting from a data source such as a database.
 *
 * @package DarlingCms\interfaces\crud
 */
interface IActionCrud
{
    /**
     * Create a new action.
     *
     * @param IAction $action The IAction implementation instance that
     *                        represents the action to create.
     *
     * @return bool True if action was created, false otherwise
     */
    public function create(IAction $action): bool;

    /**
     * Read a specified action from the database.
     *
     * @param string $actionName The name of the action to read.
     *
     * @return IAction An IAction implementation instance that represents the action,
     *                 or a default IAction implementation instance.
     */
    public function read(string $actionName): IAction;

    /**
     * Returns an array of IAction implementation instances for each
     * action stored in the database.
     *
     * @return array An array of IAction implementation instances for each
     *               action stored in the database.
     */
    public function readAll(): array;

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
    public function update(string $actionName, IAction $newAction): bool;

    /**
     * Deletes the specified action.
     *
     * @param string $actionName The name of the action to delete.
     *
     * @return bool True if action was deleted, false otherwise.
     */
    public function delete(string $actionName): bool;
}
