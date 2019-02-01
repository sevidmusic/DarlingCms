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
    /**
     * Create a new user.
     * @param IUser $user The IUser implementation instance that represents the user.
     * @return bool True if user was created, false otherwise.
     */
    public function create(IUser $user): bool;

    /**
     * Read a specified user's data.
     * @param string $userName The user's username.
     * @return IUser The IUser implementation instance that represents the user.
     */
    public function read(string $userName): IUser;

    /**
     * Return all stored Users.
     * @return array|IUser[] Array of all stored users.
     */
    public function readAll(): array;

    /**
     * Update a specified user's data.
     * @param string $userName The user's username.
     * @param IUser $newUser The IUser implementation instance that represents the user's new data.
     * @return bool True if user was update, false otherwise.
     */
    public function update(string $userName, IUser $newUser): bool;

    /**
     * Delete a specified user's data.
     * @param string $userName The user's username.
     * @return bool True if user was deleted, false otherwise.
     */
    public function delete(string $userName): bool;
}
