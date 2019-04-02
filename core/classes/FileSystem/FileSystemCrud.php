<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-23
 * Time: 21:45
 */

namespace DarlingCms\classes\FileSystem;

use DarlingCms\abstractions\FileSystem\AWorkingDirectory;
use DarlingCms\interfaces\FileSystem\IDirectoryCrud;
use DarlingCms\interfaces\FileSystem\IFileCrud;
use DarlingCms\interfaces\FileSystem\IFileSystemCrud;
use RecursiveDirectoryIterator;

/**
 * Class FileSystemCrud. Implementation of the IFileSystemCrud interface that can be used
 * to create, read, update, and delete files and directories in a specified directory and
 * it's sub-directories.
 * @package DarlingCms\classes\FileSystem
 */
class FileSystemCrud extends AWorkingDirectory implements IFileSystemCrud
{
    /**
     * @var IFileCrud Instance of a IFileCrud implementation used to perform Crud operations on files
     *                in the working directory.
     */
    private $fileCrud;

    /**
     * @var IDirectoryCrud Instance of a IDirectoryCrud implementation used to perform Crud operations on
     *                directories in the working directory.
     */
    private $directoryCrud;

    /**
     * FileSystemCrud constructor. Sets the working directory. Instantiates the
     * IFileCrud and IDirectoryCrud implementation instances used to perform
     * Crud operations on files and directories in the working directory.
     * @param string $workingDirectoryPath The path to the working directory, i.e.,
     *                                     the path to the directory this FileSystemCrud
     *                                     performs Crud operations on.
     */
    public function __construct(string $workingDirectoryPath)
    {
        parent::__construct($workingDirectoryPath);
        $this->fileCrud = new FileCrud($this->getWorkingDirectoryPath());
        $this->directoryCrud = new DirectoryCrud($this->getWorkingDirectoryPath());
    }

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
    public function createDirectory(string $directoryName, $permissions = 0644): bool
    {
        return $this->directoryCrud->createDirectory($directoryName, $permissions);
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
        return $this->directoryCrud->readDirectory($directoryName);
    }

    /**
     * Update the name of a specified directory in the working directory.
     * @param string $directoryName The name of the directory whose name is to be updated.
     * @param string $newDirectoryName The new directory name.
     * @return bool True if directory name was updated successfully, false otherwise.
     */
    public function updateDirectoryName(string $directoryName, string $newDirectoryName): bool
    {
        return $this->directoryCrud->updateDirectoryName($directoryName, $newDirectoryName);
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
        return $this->directoryCrud->updateDirectoryPermissions($directoryName, $newDirectoryPermissions);
    }

    /**
     * Delete a specified directory from the working directory.
     * @param string $directoryName The name of the directory to delete.
     * @return bool True if directory was deleted, false otherwise.
     */
    public function deleteDirectory(string $directoryName): bool
    {
        return $this->directoryCrud->deleteDirectory($directoryName);
    }

    /**
     * Returns the working directory's path, i.e., the path to the directory
     * this instance creates, reads, updates, and deletes files in.
     * @return string The working directory's path.
     */
    public function getWorkingDirectoryPath(): string
    {
        return $this->workingDirectoryPath;
    }

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
    public function createFile(string $fileName, string $fileData, $permissions = 0644): bool
    {
        return $this->fileCrud->createFile($fileName, $fileData, $permissions);
    }

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
    public function readFile(string $fileName): string
    {
        return $this->fileCrud->readFile($fileName);
    }

    /**
     * Update the file data of a specified file in the working directory.
     * @param string $fileName The name of the file whose data is to be updated.
     * @param string $newFileData The new file data.
     * @return bool True if file data was updated successfully, false otherwise.
     */
    public function updateFileData(string $fileName, string $newFileData): bool
    {
        return $this->fileCrud->updateFileData($fileName, $newFileData);
    }

    /**
     * Update the name of a specified file in the working directory.
     * @param string $fileName The name of the file whose name is to be updated.
     * @param string $newFileName The new file data.
     * @return bool True if file name was updated successfully, false otherwise.
     */
    public function updateFileName(string $fileName, string $newFileName): bool
    {
        return $this->fileCrud->updateFileName($fileName, $newFileName);
    }

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
    public function updateFilePermissions(string $fileName, int $newFilePermissions): bool
    {
        return $this->fileCrud->updateFilePermissions($fileName, $newFilePermissions);
    }

    /**
     * Delete a specified file from the working directory.
     * @param string $fileName The name of the file to delete.
     * @return bool True if file was deleted, false otherwise.
     */
    public function deleteFile(string $fileName): bool
    {
        return $this->fileCrud->deleteFile($fileName);
    }

}
