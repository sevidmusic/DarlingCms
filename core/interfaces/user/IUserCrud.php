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

    public function read(string $userName): IUser;

    public function update(string $userName, string $currentPassword, IUser $newUser, IUserPassword $newPassword): bool;

    public function delete(string $userName, string $currentPassword): bool;
}
