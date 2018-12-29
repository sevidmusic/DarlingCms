<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-20
 * Time: 19:50
 */

namespace DarlingCms\interfaces\crud;

use DarlingCms\interfaces\user\IUser;
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

    /**
     * @param IUser $user The user the password belongs to.
     * @return IUserPassword
     */
    public function read(IUser $user): IUserPassword;

    public function update(IUser $user, IUserPassword $newUserPassword): bool;

    public function delete(IUser $user): bool;
}
