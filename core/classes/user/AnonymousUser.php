<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-15
 * Time: 20:17
 */

namespace DarlingCms\classes\user;


use DarlingCms\abstractions\user\APDOCompatibleUser;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\interfaces\user\IUser;

/**
 * Class AnonymousUser. Defines an implementation of the IUser interface that defines an anonymous user
 * that is not assigned any roles or meta data.
 *
 * WARNING: This class is not meant to be extended. This implementation has a very specific goal of
 * constructing a user with no access and no data, i.e., no roles or meta data. All of the methods
 * in this implementation are declared as final, and return values are intentionally hard-coded and
 * MUST not change.
 *
 * @package DarlingCms\classes\user
 */
class AnonymousUser extends APDOCompatibleUser implements IUser
{
    final public function getUserName(): string
    {
        return 'Anonymous';
    }

    final public function getUserId(): string
    {
        return 'Anonymous';
    }

    final public function getPublicMeta(): array
    {
        return array();
    }

    final public function getPrivateMeta(): array
    {
        return array();
    }

    final public function getRoles(): array
    {
        return array();
    }

    final public function userHasRole(IRole $role): bool
    {
        return false;
    }

}
