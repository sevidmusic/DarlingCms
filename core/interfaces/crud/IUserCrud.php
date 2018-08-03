<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 7/27/18
 * Time: 11:38 PM
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\accessControl\IUser;

/**
 * Interface IUserCrud. Defines the basic contract of an object that can create, read, update, and delete stored
 * IUser implementation instances.
 * @package DarlingCms\interfaces\crud
 */
interface IUserCrud
{
    /**
     * Create a new user.
     * @param IUser $user The IUser implementation instance that represents the new user.
     * @return bool True if user was created successfully, false otherwise.
     */
    public function create(IUser $user): bool;

    /**
     * Read the specified user's stored IUser implementation instance.
     * @param string $username The user's username.
     * @return IUser The specified user's IUser implementation instance.
     */
    public function read(string $username): IUser;

    /**
     * Update a specified user's stored IUser implementation instance.
     * @param string $username The user's username.
     * @param IUser $updatedUser The updated IUser implementation instance that represents the user.
     * @return bool True if the user's stored IUser implementation instance was updated, false otherwise.
     */
    public function update(string $username, IUser $updatedUser): bool;

    /**
     * Delete a specified user's IUser implementation instance from storage.
     * @param string $username The user's username.
     * @return bool True if user was deleted, false otherwise.
     */
    public function delete(string $username): bool;
}
