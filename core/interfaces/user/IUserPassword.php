<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:29
 */

namespace DarlingCms\interfaces\user;


interface IUserPassword
{
    public function verifyPassword(IUser $user, string $password): bool;

    public function changePassword(IUser $user, string $oldPassword, string $newPassword): bool;
}
