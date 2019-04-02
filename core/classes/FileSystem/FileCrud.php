<?php

namespace DarlingCms\classes\FileSystem;

use DarlingCms\abstractions\FileSystem\AWorkingDirectory;
use DarlingCms\interfaces\FileSystem\IDirectoryCrud;
use DarlingCms\interfaces\FileSystem\IFileCrud;

/**
 * Class FileCrud. Defines an implementation of the IFileCrud interface that extends the
 * AWorkingDirectory abstract class. Instances of this class can be used to perform Crud
 * operations on files in a specified directory and it's sub-directories.
 * @package DarlingCms\classes\FileSystem
 */
class FileCrud extends AWorkingDirectory implements IFileCrud
{
    /**
     * @var IDirectoryCrud $directoryCrud Instance of an IDirectoryCrud implementation that will be used
     *                                    when it is necessary to perform Crud operations on directories
     *                                    in the specified working directory.
     */
    private $directoryCrud;

    /**
     * FileCrud constructor. Sets the working directory's path. Instantiates the IDirectoryCrud
     * implementation instance used to perform Crud operations on directories in the working directory.
     * @param string $workingDirectoryPath The path to the working directory.
     */
    public function __construct(string $workingDirectoryPath)
    {
        parent::__construct($workingDirectoryPath);
        /**
         * @devNote The IDirectoryImplementation is instantiated instead of injected to insure it's working path
         *          matches this FileCrud instances working path.
         */
        $this->directoryCrud = new DirectoryCrud($this->getWorkingDirectoryPath());
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
     * @return bool True if file was created, false otherwise.
     * @see https://www.php.net/manual/en/function.chmod.php
     */
    public function createFile(string $fileName, string $fileData, $permissions = 0644): bool
    {
        if (file_exists($this->getPathInWorkingDirectory($fileName))) {
            error_log('File Crud Error: Cannot create file "' . $fileName . '" at path "' . $this->getPathInWorkingDirectory($fileName) . '" because a file with the same name already exists at that path. Try updating instead.');
            return false;
        }
        // if there are slashes assume its a path to a sub dir within the working dir
        if (count($pathsParts = explode('/', $fileName)) > 1) {
            // determine sub dir path
            $subDirPath = (str_replace('/' . end($pathsParts), '', $fileName));
            // if sub dir(s) do(es) not exist and cannot be created log an error and return false
            if (is_dir($this->getWorkingDirectoryPath() . $subDirPath) === false && $this->directoryCrud->createDirectory($subDirPath, $permissions) === false) {
                error_log('File Crud Error: Failed to create necessary sub directories for new file "' . $fileName . '"');
                return false;
            }
        }
        // attempt to create the file
        return !empty(file_put_contents($this->getPathInWorkingDirectory($fileName), $fileData, LOCK_EX));
    }

    /**
     * Read a file in the working directory.
     *
     * Note: This method will return an empty string if the specified file
     * file does not exist, or if the file does not have any data, therefore,
     * it is not reliable to test if the returned value is empty to determine
     * if this method was successful.
     *
     * Note: If the specified file does not exists an error will be logged and
     * an empty string will be returned.
     *
     * @param string $fileName The name of the file to read.
     * @return string The file's data. Warning: An empty string will be returned
     *                if the file does not have any data or if the file does not
     *                exist!
     */
    public function readFile(string $fileName): string
    {
        if (file_exists($this->getPathInWorkingDirectory($fileName)) === true) {
            return file_get_contents($this->getPathInWorkingDirectory($fileName));
        }
        error_log('File Crud Error: Could not read file ' . $fileName . ' at path ' . $this->getPathInWorkingDirectory($fileName));
        return '';
    }

    /**
     * Update the file data of a specified file in the working directory.
     * @param string $fileName The name of the file whose data is to be updated.
     * @param string $newFileData The new file data.
     * @return bool True if file data was updated successfully, false otherwise.
     */
    public function updateFileData(string $fileName, string $newFileData): bool
    {
        $originalFilePermissions = fileperms($this->getPathInWorkingDirectory($fileName));
        if ($this->deleteFile($fileName) === true) {
            return $this->createFile($fileName, $newFileData, $originalFilePermissions);
        }
        error_log('File Crud Error: Could not update file ' . $fileName . ' at path ' . $this->getPathInWorkingDirectory($fileName));
        return false;
    }

    /**
     * Update the name of a specified file in the working directory.
     * @param string $fileName The name of the file whose name is to be updated.
     * @param string $newFileName The new file data.
     * @return bool True if file name was updated successfully, false otherwise.
     */
    public function updateFileName(string $fileName, string $newFileName): bool
    {
        return rename($this->getPathInWorkingDirectory($fileName), $this->getPathInWorkingDirectory($newFileName));
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
        return chmod($this->getPathInWorkingDirectory($fileName), $newFilePermissions);
    }

    /**
     * Delete a specified file from the working directory.
     * @param string $fileName The name of the file to delete.
     * @return bool True if file was deleted, false otherwise.
     */
    public function deleteFile(string $fileName): bool
    {
        return unlink($this->getPathInWorkingDirectory($fileName));
    }

}
