<?php


namespace DarlingCms\interfaces\FileSystem;

/**
 * Interface IFileCrud. Defines the basic contract of an object that can perform
 * Crud operations on files in a specified directory.
 *
 * @package DarlingCms\interfaces\FileSystem
 */
interface IFileCrud
{
    /**
     * Returns the working directory's path, i.e., the path to the directory
     * this instance creates, reads, updates, and deletes files in.
     * @return string The working directory's path.
     */
    public function getWorkingDirectoryPath(): string;

    /**
     * Create a new file in the working directory.
     * @param string $fileName The name of the file to create.
     * @param string $fileData The data to write to the new file.
     * @param int $permissions The octal value that represents the permissions to assign to the file.
     *                         Value MUST be an octal number that represents the appropriate unix mode.
     *                         For more information see the PHP manual entry for the chmod() function,
     *                         or use the 'man 1 chmod' or 'man 2 chmod' commands from the command line.
     *                         Defaults to 0644.
     * @return bool True if file was created, false otherwise.
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function createFile(string $fileName, string $fileData, $permissions = 0644): bool;

    /**
     * Read a file in the working directory.
     *
     * Note: This method will return an empty string if the specified file
     * file does not exist, or if the file does not have any data, therefore,
     * it is not reliable to test if the returned value is empty to determine
     * if this method was successful.
     *
     * @param string $fileName The name of the file to read.
     * @return string The file's data. Warning: An empty string will be returned
     *                if the file does not have any data or if the file does not
     *                exist!
     */
    public function readFile(string $fileName): string;

    /**
     * Update the file data of a specified file in the working directory.
     * @param string $fileName The name of the file whose data is to be updated.
     * @param string $newFileData The new file data.
     * @return bool True if file data was updated successfully, false otherwise.
     */
    public function updateFileData(string $fileName, string $newFileData): bool;

    /**
     * Update the name of a specified file in the working directory.
     * @param string $fileName The name of the file whose name is to be updated.
     * @param string $newFileName The new file data.
     * @return bool True if file name was updated successfully, false otherwise.
     */
    public function updateFileName(string $fileName, string $newFileName): bool;

    /**
     * Update the permissions of a specified file in the working directory.
     * @param string $fileName The name of the file whose permissions are to be updated.
     * @param int $newFilePermissions The octal value that represents the new permissions to assign to the file.
     *                                Value MUST be an octal number that represents the appropriate unix mode.
     *                                For more information see the PHP manual entry for the chmod() function,
     *                                or use the 'man 1 chmod' or 'man 2 chmod' commands from the command line.
     * @return bool True if file permissions were updated successfully, false otherwise.
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function updateFilePermissions(string $fileName, int $newFilePermissions): bool;

    /**
     * Delete a specified file from the working directory.
     * @param string $fileName The name of the file to delete.
     * @return bool True if file was deleted, false otherwise.
     */
    public function deleteFile(string $fileName): bool;

}
