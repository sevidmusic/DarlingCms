<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:37
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\privilege\IRole;
/**
 * Interface IRoleCrud. Defines the contract of an object that is responsible for
 * mapping IRole implementation instances so they can be created in, read from,
 * updated in, and deleted from a data source such as a database.
 * @package DarlingCms\interfaces\crud
 */
interface IRoleCrud
{
    public function create(IRole $role): bool;

    public function read(string $roleName): IRole;

    public function update(string $roleName, IRole $newRole): bool;

    public function delete(string $roleName): bool;
}
