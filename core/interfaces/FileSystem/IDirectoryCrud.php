<?php


namespace DarlingCms\interfaces\FileSystem;

use RecursiveDirectoryIterator;

/**
 * Interface IDirectoryCrud. Defines the basic contract of an object that can perform
 * Crud operations on directories in a specified directory.
 * @package DarlingCms\interfaces\FileSystem
 */
interface IDirectoryCrud
{
    /**
     * Returns the working directory's path, i.e., the path to the directory
     * this instance creates, reads, updates, and deletes directories in.
     * @return string The working directory's path.
     */
    public function getWorkingDirectoryPath(): string;

    /**
     * Create a new directory in the working directory.
     * @param string $directoryName The name of the directory to create.
     * @param int $permissions The octal value that represents the permissions to assign to the directory.
     *                         Value MUST be an octal number that represents the appropriate unix mode.
     *                         For more information see the PHP manual entry for the chmod() function,
     *                         or use the 'man 1 chmod' or 'man 2 chmod' commands from the command line.
     *                         Defaults to 0644.
     * @return bool True if directory was created, false otherwise.
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function createDirectory(string $directoryName, $permissions = 0644): bool;

    /**
     * Read a directory in the working directory. This method will return
     * an instance of a RecursiveDirectoryIterator that represents the
     * specified directory.
     *
     * @param string $directoryName The name of the directory to read.
     * @return RecursiveDirectoryIterator An instance of a RecursiveDirectoryIterator that
     *                                     represents the specified directory.
     */
    public function readDirectory(string $directoryName): RecursiveDirectoryIterator;

    /**
     * Update the name of a specified directory in the working directory.
     * @param string $directoryName The name of the directory whose name is to be updated.
     * @param string $newDirectoryName The new directory name.
     * @return bool True if directory name was updated successfully, false otherwise.
     */
    public function updateDirectoryName(string $directoryName, string $newDirectoryName): bool;

    /**
     * Update the permissions of a specified directory in the working directory.
     * @param string $directoryName The name of the directory whose permissions are to be updated.
     * @param int $newDirectoryPermissions The octal value that represents the new permissions to
     *                                     assign to the directory. Value MUST be an octal number
     *                                     that represents the appropriate unix mode. For more
     *                                     information see the PHP manual entry for the chmod()
     *                                     function, or use the 'man 1 chmod' or 'man 2 chmod'
     *                                     commands from the command line.
     * @return bool True if directory permissions were updated successfully, false otherwise.
     */
    public function updateDirectoryPermissions(string $directoryName, int $newDirectoryPermissions): bool;

    /**
     * Delete a specified directory from the working directory.
     * @param string $directoryName The name of the directory to delete.
     * @return bool True if directory was deleted, false otherwise.
     */
    public function deleteDirectory(string $directoryName): bool;

}
