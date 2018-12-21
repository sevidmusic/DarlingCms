<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:32
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\user\IUser;

/**
 * Interface IUserCrud. Defines the contract of an object that is responsible for
 * creating, reading, updating, and deleting IUser implementation instances from
 * a data source such as a database.
 * @package DarlingCms\interfaces\crud
 */
interface IUserCrud
{
    public function create(IUser $user): bool;

    public function read(string $userName): IUser;

    public function update(string $userName, IUser $newUser): bool;

    public function delete(string $userName): bool;
}
