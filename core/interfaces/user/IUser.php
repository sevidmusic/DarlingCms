<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-12
 * Time: 14:27
 */

namespace DarlingCms\interfaces\user;


interface IUser
{
    public function getUserName(): string;

    public function getUserId(): string;

    public function getPublicMeta(): array;

    public function getPrivateMeta(): array;

    public function userHasRole(/* @todo ! *ACTIVE* Implement $role parameter. | IRole $role | */): bool;


}
