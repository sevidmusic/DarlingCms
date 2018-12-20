<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:27
 */

namespace DarlingCms\interfaces\user;


use DarlingCms\interfaces\privilege\IRole;

interface IUser
{
    public function getUserName(): string;

    public function getUserId(): string;

    public function getPublicMeta(): array;

    public function getPrivateMeta(): array;

    public function getRoles(): array;

    public function userHasRole(IRole $role): bool;

}
