<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:28
 */

namespace DarlingCms\interfaces\user;


interface IUserLogin
{

    public function login(IUser $user, IUserPassword $password): bool;

    public function logout(string $username): bool;

    public function isLoggedIn(string $userName): bool;

}
