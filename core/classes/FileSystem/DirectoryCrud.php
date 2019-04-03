<?php

namespace DarlingCms\classes\FileSystem;

use DarlingCms\abstractions\FileSystem\AWorkingDirectory;
use DarlingCms\interfaces\FileSystem\IDirectoryCrud;
use RecursiveDirectoryIterator;

/**
 * Class DirectoryCrud. Defines an implementation of the IDirectoryCrud interface that
 *  extends the AWorkingDirectory abstract class. Instances of this class can be used
 * to perform Crud operations on directories in a specified directory and it's
 * sub-directories.
 * @package DarlingCms\classes\FileSystem
 */
class DirectoryCrud extends AWorkingDirectory implements IDirectoryCrud
{
    /**
     * Returns the working directory's path, i.e., the path to the directory
     * this instance creates, reads, updates, and deletes directories in.
     * @return string The working directory's path.
     */
    public function getWorkingDirectoryPath(): string
    {
        return $this->workingDirectoryPath;
    }

    /**
     * Create a new directory in the working directory.
     * @param string $directoryName The name of the directory to create.
     * @param int $permissions The octal value that represents the permissions to assign to the directory.
     *                         Value MUST be an octal number that represents the appropriate unix mode.
     *                         For more information see the PHP manual entry for the chmod() function,
     *                         or use the 'man 1 chmod' or 'man 2 chmod' commands from the command line.
     * @return bool True if directory was created, false otherwise.
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function createDirectory(string $directoryName, $permissions = 0644): bool
    {
        if (is_dir($this->getPathInWorkingDirectory($directoryName)) === true) {
            error_log('Directory Crud Error: Cannot create directory ' . $directoryName . ' at path ' . $this->getPathInWorkingDirectory($directoryName) . ' because a directory with the same name already exists. Try updating instead.');
            return false;
        }
        return mkdir($this->getPathInWorkingDirectory($directoryName), $permissions, true);
    }

    /**
     * Read a directory in the working directory. This method will return
     * an instance of a RecursiveDirectoryIterator that represents the
     * specified directory.
     *
     * @param string $directoryName The name of the directory to read.
     * @return RecursiveDirectoryIterator An instance of a RecursiveDirectoryIterator that
     *                                     represents the specified directory.
     */
    public function readDirectory(string $directoryName): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator($this->getPathInWorkingDirectory($directoryName));
    }

    /**
     * Update the name of a specified directory in the working directory.
     * @param string $directoryName The name of the directory whose name is to be updated.
     * @param string $newDirectoryName The new directory name.
     * @return bool True if directory name was updated successfully, false otherwise.
     */
    public function updateDirectoryName(string $directoryName, string $newDirectoryName): bool
    {
        return rename($this->getPathInWorkingDirectory($directoryName), $this->getPathInWorkingDirectory($newDirectoryName));
    }

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
    public function updateDirectoryPermissions(string $directoryName, int $newDirectoryPermissions): bool
    {
        return chmod($this->getPathInWorkingDirectory($directoryName), $newDirectoryPermissions);
    }

    /**
     * Delete a specified directory from the working directory.
     * @param string $directoryName The name of the directory to delete.
     * @return bool True if directory was deleted, false otherwise.
     */
    public function deleteDirectory(string $directoryName): bool
    {
        $pathInWorkingDirectory = $this->getPathInWorkingDirectory($directoryName);
        if (empty($pathInWorkingDirectory) === false && $pathInWorkingDirectory !== '/') {
            $directoryContents = array_diff(scandir($pathInWorkingDirectory), array('.', '..'));
            foreach ($directoryContents as $item) {
                (is_dir("$pathInWorkingDirectory/$item")) ? $this->deleteDirectory($directoryName . '/' . $item) : unlink($pathInWorkingDirectory . '/' . $item);
            }
            if (rmdir($pathInWorkingDirectory) === true) {
                return true;
            }
        }
        error_log('Directory Crud Error: Could not delete directory "' . $directoryName . '"" at path "' . $pathInWorkingDirectory . '"');
        return false;
    }

}
