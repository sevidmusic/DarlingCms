<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:38
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\privilege\IAction;

/**
 * Interface IActionCrud. Defines the contract of an object that is responsible for
 * mapping IAction implementation instances so they can be created in, read from,
 * updated in, and deleted from a data source such as a database.
 * @package DarlingCms\interfaces\crud
 */
interface IActionCrud
{
    public function create(IAction $action): bool;

    public function read(string $actionName): IAction;

    public function readAll():array;

    public function update(string $actionName, IAction $newAction): bool;

    public function delete(string $actionName): bool;
}
