<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:50
 */

namespace DarlingCms\interfaces\crud;

use DarlingCms\interfaces\user\IUserPassword;

/**
 * Interface IUserPasswordCrud. Defines the contract of an object that is responsible for
 * mapping IUserPassword implementation instances so they can be created in, read from,
 * updated in, and deleted from a data source such as a database.
 * @package DarlingCms\interfaces\crud
 */
interface IUserPasswordCrud
{

    public function create(IUserPassword $userPassword): bool;

    public function read(string $passwordId): IUserPassword;

    public function update(string $passwordId, IUserPassword $newUserPassword): bool;

    public function delete(string $passwordId): bool;
}
