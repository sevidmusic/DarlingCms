<?php


namespace DarlingCms\abstractions\FileSystem;

/**
 * Class AWorkingDirectory. Defines an abstract class that can be extended by objects that
 * represent or utilize a working directory.
 * @package DarlingCms\abstractions\FileSystem
 */
abstract class AWorkingDirectory
{
    /**
     * @var string The working directory's path.
     */
    protected $workingDirectoryPath;

    /**
     * AWorkingDirectory constructor. Sets the working directory.
     * @param string $workingDirectoryPath The working directory's path.
     */
    public function __construct(string $workingDirectoryPath)
    {
        $this->getDefaultPath();
        switch (empty($workingDirectoryPath) || !is_dir($workingDirectoryPath)) {
            case false:
                $this->workingDirectoryPath = $this->addSlashIfMissing($workingDirectoryPath);
                break;
            default:
                error_log('Working Directory Error: Attempt to access path "' . $workingDirectoryPath . '" failed. Using default path "' . $this->getDefaultPath() . '""');
                $this->workingDirectoryPath = $this->getDefaultPath();

        }
    }

    /**
     * Returns the working directory's path.
     * @return string The working directory's path.
     */
    abstract public function getWorkingDirectoryPath(): string;

    /**
     * Returns a default path that can be safely assigned as the working directory.
     *
     * Note: This method prefers to return the upload_tmp_dir directory path set in
     * PHP's ini file, but if it is unable to do so it will return the path returned
     * by PHP's sys_get_temp_dir() function.
     *
     * @return string A default path that is safe to assign as the working directory.
     * @see https://www.php.net/manual/en/function.ini-get.php
     * @see https://www.php.net/manual/en/function.sys-get-temp-dir.php
     */
    protected function getDefaultPath(): string
    {
        // prefer php tmp dir as set in ini file
        if (is_dir($this->addSlashIfMissing(ini_get('upload_tmp_dir'))) === true) {
            return $this->addSlashIfMissing(ini_get('upload_tmp_dir'));
        }
        // use system tmp dir if php tmp dir is not available
        return $this->addSlashIfMissing(sys_get_temp_dir());
    }

    /**
     * Generates the full path to the specified file or directory in the working directory
     * from the specified relative path.
     *
     * Note: This method does not check if a file or directory exists, and will return a path
     * for the specified file or directory regardless of whether or not he specified file or
     * directory actually exists in the working directory.
     *
     * Warning: This method may or may not include an ending slash in directory paths.
     *
     * Example Usage:
     *
     * // Assuming working dir path is "/Some/Working/Dir/Path"
     *
     * $this->getPathInWorkingDirectory('file.txt'); // returns "/Some/Working/Dir/Path/file.txt"
     *
     * $this->getPathInWorkingDirectory('/file.txt'); // returns "/Some/Working/Dir/Path/file.txt"
     *
     * $this->getPathInWorkingDirectory('/SubDir/subFile.txt'); // returns "/Some/Working/Dir/Path/SubDir/subFile.txt"
     *
     * $this->getPathInWorkingDirectory('SubDir/subFile.txt'); // returns "/Some/Working/Dir/Path/SubDir/subFile.txt"
     *
     * $this->getPathInWorkingDirectory('SubDir/SubSubDir/SubSubSubDir'); // returns "/Some/Working/Dir/Path/SubDir/SubSubDir/SubSubSubDir"
     *
     * @param string $relativePath The relative path to the file or directory under
     *                             the working directory.
     * @return string The full path to the specified file or directory in the working directory.
     */
    protected function getPathInWorkingDirectory(string $relativePath)
    {
        return trim(str_replace('//', '/', $this->getWorkingDirectoryPath() . $relativePath));
    }

    /**
     * Add an ending slash to the specified path if it does not already have one.
     *
     * For Example:
     *
     * $this->addSlashIfMissing('/Some/Path/Without/Slash'); // returns "/Some/Path/Without/Slash/"
     *
     * $this->addSlashIfMissing('/Some/Path/With/Slash/'); // returns "/Some/Path/With/Slash/"
     *
     * @param string $path The path to add a slash to.
     * @return string The path, with an added ending slash if the specified path
     *                did not already have one.
     */
    protected function addSlashIfMissing(string $path): string
    {
        return substr($path, 0, -1) === '/' ? $path : $path . '/';
    }

}
