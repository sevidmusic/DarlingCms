<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-23
 * Time: 00:08
 */

namespace DarlingCms\classes\installer;


use DarlingCms\classes\FileSystem\FileSystemCrud;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\FileSystem\IFileSystemCrud;
use DarlingCms\interfaces\installer\IInstaller;

/**
 * Class RequiredDirectoryInstaller. Defines an implementation of the IInstaller interface
 * that can be used to install the directories required by the Darling Cms.
 * @package DarlingCms\classes\installer
 */
class RequiredDirectoryInstaller implements IInstaller
{

    /**
     * @var array Array of the names of the required directories that will be
     *            installed/un-installed by this instance.
     */
    private $requiredDirectoryNames = array('apps', 'js', 'themes');

    /**
     * @var IFileSystemCrud $fileSystemCrud Instance of an IFileSystemCrud implementation used to install
     *                                      and uninstall the required directories.
     */
    private $fileSystemCrud;

    /**
     * RequiredDirectoryInstaller constructor. Instantiates the IFileSystemCrud implementation used
     * to install the required directories.
     */
    public function __construct()
    {
        $this->fileSystemCrud = new FileSystemCrud(CoreValues::getSiteRootDirPath());
    }


    /**
     * Perform installation. Install the required directories.
     *
     * Note: Required directories will only be installed if they do not already exist.
     *
     * Note: This method will log an error for any required directory that cannot be installed.
     *
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool
    {
        $status = array();
        foreach ($this->requiredDirectoryNames as $directoryName) {
            array_push($status, $this->fileSystemCrud->createDirectory($directoryName, 0755));
            if (end($status) === false) {
                error_log('Required Directory Installer Error: Failed to install the ' . $directoryName . ' directory because a directory with the same name already exists.');
            }
        }
        return !in_array(false, $status, true);
    }

    /**
     * Perform un-installation. Un-install the required directories.
     *
     * Note: This method will only un-install the required directories
     * if they are empty, if they are not empty an error will be logged
     * and the required directories will not be deleted.
     *
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        $status = array();
        foreach ($this->requiredDirectoryNames as $directoryName) {
            /**
             * @devNote: Only delete if directory is empty, this is to insure any apps, themes, or
             * js libraries that may have been manually installed are not removed by mistake.
             */
            if ($this->directoryIsEmpty($directoryName) === true) {
                array_push($status, $this->fileSystemCrud->deleteDirectory($directoryName));
            } else {
                error_log('Required Directory Installer Error: Failed to un-install the "' . $directoryName . '" directory because it is not empty.');
                array_push($status, false);
            }
        }
        return !in_array(false, $status, true);
    }

    /**
     * Determines whether or not a specified directory in the working directory is empty.
     * @param string $directoryName The name of the directory.
     * @return bool True if directory is empty, false otherwise.
     */
    private function directoryIsEmpty(string $directoryName): bool
    {
        return (empty(array_diff(scandir($this->fileSystemCrud->getWorkingDirectoryPath() . $directoryName), array('.', '..', '.DS_Store'))) === true);
    }
}
