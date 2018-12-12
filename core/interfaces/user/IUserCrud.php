<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:29
 */

namespace DarlingCms\interfaces\user;


interface IUserCrud
{
    public function create(IUser $user, IUserPassword $password): bool;

    public function read(string $userName);

    public function update(string $userName, IUser $user, IUserPassword $password, array $options = array());

    public function delete(string $userName);
}
