<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 11/3/18
 * Time: 3:11 PM
 */

namespace DarlingCms\classes\FileSystem;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

/**
 * Class ZipArchiveUtility. Defines an extension of the ZipArchive class that can be
 * used to create zip archives, un-archive zip archives, read or extract files from
 * zip archives, or to obtain information about zip archives.
 * @package DarlingCms\classes\FileSystem
 * @see ZipArchiveUtility::zip()
 * @see ZipArchiveUtility::unzip()
 */
class ZipArchiveUtility extends ZipArchive
{
    /**
     * Create a zip archive of the specified path, and if specified, delete the original item.
     *
     * WARNING: If zip is successful, the original item will be deleted unless the $deleteOriginal
     * parameter is explicitly set to false.
     *
     * @param string $targetPath The path to the item to zip.
     * @param bool $deleteOriginal Determines whether or not the original item should be deleted. (Defaults to true)
     * @return bool True if the item was zipped, and, if specified, the original item was deleted, false otherwise.
     */
    public function zip(string $targetPath, bool $deleteOriginal = true): bool
    {
        $zipArchivePath = $targetPath . '.zip';
        // Make sure archive is possible.
        if ($this->archiveIsPossible($targetPath, $zipArchivePath) === false) {
            return false; // @devNote: archiveIsPossible() will handle logging errors that occur at this point
        }
        // Archive, cleanup, and return status.
        return $this->cleanup($this->archive($targetPath, $zipArchivePath), $targetPath, $zipArchivePath, $deleteOriginal);
    }

    /**
     * Determines if it is possible to create an archive of the specified $targetPath at the
     * specified $zipArchivePath.
     * @param string $targetPath The path to archive, e.g., "/Some/Path/To/Archive"
     * @param string $zipArchivePath The path to save the archive to, e.g., "/Some/Path/To/Save/The/Archive.zip"
     * @return bool True if it is possible to archive the target path at the zip archive path, false otherwise.
     */
    private function archiveIsPossible(string $targetPath, string $zipArchivePath): bool
    {
        // Make sure the zip extension is loaded.
        if (extension_loaded('zip') === false) {
            error_log('Zip Archive Utility Error: Zip extension does not exist.');
            return false;
        }
        // Make sure a file or directory exists at the $targetPath.
        if (file_exists($targetPath) === false) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath} because no file or directory exists at that path.");
            return false;
        }
        // Make sure a zip archive does not already exist at the $zipArchivePath.
        if (file_exists($zipArchivePath) === true) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath}. An archive at {$zipArchivePath} already exists.");
            return false;
        }
        // Return true, it is possible to create an archive of the $targetPath at the $zipArchivePath.
        return true;
    }

    /**
     * Attempts to perform any cleanup that should be done after the archive() method has been called.
     *
     * WARNING: This method is intended to be called by the zip() method after the archive() method
     * has been called, using it in any other context may have unintended consequences!
     *
     * @param bool $archiveStatus The boolean value returned by the archive method.
     * @param string $targetPath The path to the item that was archived.
     * @param string $zipArchivePath The path to the zip archive.
     * @param bool $deleteOriginal Set to true if original item should be deleted, otherwise set to false.
     * @return bool True if cleanup was successful, false otherwise.
     */
    private function cleanup(bool $archiveStatus, string $targetPath, string $zipArchivePath, bool $deleteOriginal): bool
    {
        // Close archive, if archive cannot be closed log error, and return true if archive was removed, false otherwise.
        if ($this->close() === false) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath}. Archive could not be closed.");
            return unlink($zipArchivePath);
        }
        // Make sure archive exists, if it does not exist, log error, and return true as there is nothing to clean up.
        if (file_exists($zipArchivePath) === false) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath}. The archive could not be created at {$zipArchivePath}");
            return true;
        }
        // If any errors occurred during archiving, log error, and return true if archive was removed, false otherwise.
        if ($archiveStatus === false) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath} because errors occurred during the archiving process.");
            return unlink($zipArchivePath);
        }
        // If specified, attempt to delete original item.
        if ($deleteOriginal === true) {
            // If original item could not be removed, log error and return false.
            if ($this->removeDir($targetPath) !== true) {
                error_log("Zip Archive Utility Error: Failed to remove original item {$targetPath}");
                return false;
            }
        }
        // Cleanup was successful, return true.
        return true;
    }

    /**
     * Removes a directory and it's contents.
     *
     * WARNING: This method will attempt to restore the directory if an error occurs after
     * some files or sub-directories have already been removed, however, it is important
     * NOT to rely on this as it may be impossible to restore some items once they have
     * been removed.
     *
     * @param string $path The path to the directory to remove.
     * @return bool True if the directory and all of it's contents were removed, false otherwise.
     * @todo make this method private, and replace uses of it outside of this class with an alternative
     */
    public static function removeDir($path): bool
    {
        // Attempt to remove all files.
        if (self::removeFilesFromDir($path) === false) {
            error_log("ZipArchiveUtility Error: Failed to remove all files from directory at path {$path}. The directory could not be removed.");
            return false; // @devNote: Stop on error, it is safer to stop than to proceed on error to avoid corrupting the filesystem.
        }
        // Attempt to remove all sub directories. // @todo need to restore sub-directories on failure
        foreach (self::scanDir($path) as $item) {
            $pathToItem = $path . '/' . $item;
            if (is_dir($pathToItem) === true) {
                if (self::removeDir($pathToItem) === false) {
                    error_log("ZipArchiveUtility error: Failed to remove sub-directory {$pathToItem}. WARNING: Some sub-directories may have already been removed and may not have been restored!");
                    return false; // @devNote: Stop on error, it is safer to stop than to proceed on error to avoid corrupting the filesystem.
                }
            }
        }
        if (rmdir($path) === false) {
            error_log("ZipArchiveUtility error: Failed to remove directory {$path}");
            return false;
        }
        return true;
    }

    /**
     * Remove all files from a directory.
     *
     * WARNING: This method will attempt to restore all files if an error occurs after
     * some files have already been removed, however, it is important NOT to rely on this
     * as it may be impossible to restore some files once they have been removed.
     *
     * @param string $path Path to the directory whose files are to be removed.
     * @return bool True if all files were removed, false otherwise.
     */
    private static function removeFilesFromDir(string $path): bool
    {
        // Initialize array to store file contents in case of failure so files can be re-created.
        $fileContents = array();
        // Initialize file removal status array so to track success or failure of each file deletion
        $fileRemovalStatus = array();
        foreach (self::scanDir($path) as $item) {
            $pathToItem = $path . '/' . $item;
            if (is_file($pathToItem) === true) {
                // Store file contents in case of failure so it can be re-created, index by path.
                $fileContents[$pathToItem] = file_get_contents($pathToItem);
                if (unlink($pathToItem) === false) {
                    // If an error occurred, log status in file removal array.
                    array_push($fileRemovalStatus, false);
                    error_log("ZipArchiveUtility error: Failed to remove file {$pathToItem}");
                }
            }
        }
        /**
         * File removal status array should be empty if all files were successfully deleted, if it is not
         * empty then an error occurred and all files should be restored.
         */
        if (empty($fileRemovalStatus) === false) {
            foreach ($fileContents as $filePath => $fileContent) {
                if (empty(file_put_contents($filePath, $fileContent, LOCK_EX)) === true) {
                    error_log("ZipArchiveUtility Error: Failed to restore file at path {$filePath} after error occurred.");
                }
            }
        }
        return empty($fileRemovalStatus);
    }

    /**
     * Returns an array of a directory's contents excluding ".", "..", and ".DS_Store".
     * @param string $path Path to the directory to scan.
     * @return array Array of the directory's contents.
     */
    private static function scanDir(string $path): array
    {
        if (is_dir($path) === false) {
            error_log("Zip Archive Utility Error: Failed to scan directory at path {$path} because it does not exist.");
            return array();
        }
        return array_diff(scandir($path), array('.', '..')); // @devNote: Do not exclude .DS_Store or else the removeDir() method will fail.
    }

    /**
     * Create a zip archive of the specified $targetPath at the specified $zipArchivePath.
     * @param string $targetPath The path to archive, e.g., "/Some/Path/To/Archive"
     * @param string $zipArchivePath The path to save the archive to, e.g., "/Some/Path/To/Save/The/Archive.zip"
     * @return bool True if the specified $targetPath was archived at the specified $zipArchivePath, false otherwise.
     */
    private function archive(string $targetPath, string $zipArchivePath): bool
    {
        // Prepare archive.
        if ($this->prepareArchive($targetPath, $zipArchivePath) === false) {
            return false; // @devNote: prepareArchive() will handle logging errors that occur at this point
        }
        // Initialize $status var to track success or failure of archiving the $targetPath.
        $status = false;
        /**
         * If $targetPath is a directory, add the $targetPath's files and sub-directories to the
         * archive via the archiveDir() method.
         */
        if (is_dir($targetPath) === true) {
            $status = $this->archiveDir($targetPath);
        }
        // If $targetPath is a file, add the $targetPath to the archive via the archiveFile() method.
        if (is_file($targetPath) === true) {
            $status = $this->archiveFile($targetPath);
        }
        /**
         * If status is false, or the status string is not "No error", then archiving failed,
         * log error and return false.
         */
        if ($status !== true || $this->getStatusString() !== 'No error') {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath}. ZipArchive Error \"" . ($this->getStatusString() === 'No error' ? 'The specified path cannot be archived because it does not exists, or is not a file or a directory.' : $this->getStatusString()) . "\"");
            return false;
        }
        return true;
    }

    /**
     * Prepares the archive. This method will return true if the archive was prepared successfully,
     * false otherwise.
     *
     * WARNING: This method is intended to be called by the archive() method,
     * using it in any other context may have unintended consequences!
     *
     * @param string $targetPath The path to archive, e.g., "/Some/Path/To/Archive"
     * @param string $zipArchivePath The path to save the archive to, e.g., "/Some/Path/To/Save/The/Archive.zip"
     * @return bool True if archive was prepared, false otherwise.
     */
    private function prepareArchive(string $targetPath, string $zipArchivePath): bool
    {
        /**
         * Attempt to open a new zip archive at the specified $zipArchivePath, if the new zip archive
         * could not be opened, log an error and return false.
         */
        if ($archiveError = $this->open($zipArchivePath, ZipArchive::CREATE) !== true) {
            error_log("Zip Archive Utility Error: Failed to archive {$targetPath}. Failed to open archive at {$zipArchivePath} | Status: {$this->getStatusString()} | ZipArchive Error Code: {$archiveError}");
            return false;
        }
        // The new zip archive was opened successfully at the specified $zipArchivePath, return true.
        return true;
    }

    /**
     * Adds the files and sub-directories under the specified $targetPath to the currently open zip archive.
     *
     * WARNING: This method is intended to be called by the archive() method,
     * using it in any other context may have unintended consequences!
     *
     * @param string $directoryPath The path to the directory whose files and sub-directories will be
     *                           added to the zip archive.
     * @return bool True if all the files and sub-directories under the specified $targetPath were
     *              added to the currently open zip archive, false otherwise.
     */
    private function archiveDir(string $directoryPath): bool
    {
        $status = array();
        // Add the $directoryPath's sub-directories and files to the currently open zip archive.
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath), RecursiveIteratorIterator::SELF_FIRST);
        /**
         * @var SplFileInfo $splFileInfo The SplFileInfo instance for each file.
         */
        foreach ($files as $splFileInfo) {
            // Determine relative path for current file.
            $relativePath = substr($splFileInfo->getRealPath(), strlen($directoryPath) + 1);
            // Ignore ".", "..", and ".DS_Store" folders
            if (in_array($splFileInfo->getFilename(), array('.', '..', '.DS_Store'))) {
                continue;
            }
            /**
             * Handle files first so sub-directory structures are created automatically.
             * @see https://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php
             * for a good post on this.
             */
            if ($splFileInfo->isDir() === false) {
                array_push($status, $this->archiveFile($splFileInfo->getRealPath(), $relativePath));
            }
            // Handle empty directories.
            if (is_dir($splFileInfo->getRealPath()) === true) {
                array_push($status, $this->addEmptyDir($relativePath));
            }
        }
        return in_array(false, $status, true) === false;
    }

    /**
     * Adds the file located at the specified $filePath to the currently open zip archive.
     *
     * Note: This method will only work for files, for directories use the archiveDir() method.
     *
     * @param string $filePath The path to the file to archive, e.g., "/Some/Path/To/A/File/To/Archive".
     * @param string $relativePath If specified, this value will be used as the file's local
     *                             name in the zip archive, otherwise the the local name will
     *                             be determined by the basename of the $filePath.
     * @return bool True if the file located at the specified $filePath was added to the currently
     *              open zip archive, false otherwise.
     * @see \ZipArchive::addFromString()
     * @see basename()
     */
    private function archiveFile(string $filePath, string $relativePath = ''): bool
    {
        if (is_file($filePath) === false) {
            error_log('Zip Archive Utility Error: Failed to archive ' . $filePath . ' because it is not a file.');
            return false;
        }
        return $this->addFromString((empty($relativePath) === false ? $relativePath : basename($filePath)), file_get_contents($filePath));
        //return $this->addFile($filePath, basename($filePath)); // @devNote: Keep for reference as this is an alternative approach.
    }

    /**
     * Unzip an archive at a specified path, and on success delete the original archive.
     * @param string $zipArchivePath The path to the zip archive.
     * @return bool True if specified zip archive was unzipped, false otherwise.
     */
    public function unzip(string $zipArchivePath): bool
    {
        $extractionPath = str_replace('.zip', '', $zipArchivePath);
        if (file_exists($extractionPath) === true) {
            error_log("Zip Archive Utility Error: Failed to un-archive {$zipArchivePath} because {$extractionPath} already exists.");
            return false;
        }
        if (file_exists($zipArchivePath) === true) {
            $this->open($zipArchivePath);
            if ($this->extractTo($extractionPath) === false) {
                error_log("Zip Archive Utility Error: Failed to un-archive {$zipArchivePath}. Extraction to {$extractionPath} failed. Zip Archive Error: {$this->getStatusString()}");
                $this->close();
                return false;
            }
            $this->close();
            unlink($zipArchivePath);
            return file_exists($zipArchivePath) === false;
        }
        return false;
    }

    /**
     * Determines if the specified directory exists in the specified zip.
     * @param string $zipArchivePath The path to the zip archive to search in.
     * @param string $dirName The name of the directory to search for.
     * @return bool True if the specified directory exists in the specified zip, false otherwise.
     */
    public function zipHasDir(string $zipArchivePath, string $dirName): bool
    {
        if (file_exists($zipArchivePath) !== true) {
            error_log("Zip Archive Utility Error: Could not determine if directory {$dirName} exists in the {$zipArchivePath} archive because no such archive exists.");
            return false;
        }
        if ($this->open($zipArchivePath) === TRUE && $this->locateName($dirName . '/') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Extract the specified file from the specified zip archive to the specified extraction path.
     * @param string $zipArchivePath The path to the zip archive to extract the file from.
     * @param string $extractionPath The path to extract the file to.
     * @param string $fileName The name of the file to extract.
     * @param bool $overwrite If set to true, the extracted file will overwrite any file tha already
     *                        exists at the $extractionPath, if set to false the file will not be
     *                        extracted if a file already exists at the $extractionPath.
     * @return bool True if the specified file was extracted from the specified zip archive to the
     *              specified extraction path, false otherwise.
     */
    public function extractFileFromZip(string $zipArchivePath, string $extractionPath, string $fileName, bool $overwrite = false): bool
    {
        if ($overwrite === false && file_exists($extractionPath . '/' . $fileName) === true) {
            error_log("Zip Archive Utility Error: Failed to extract file {$fileName} from {$zipArchivePath} because the file {$extractionPath}/{$fileName} already exists.");
            return false;
        }
        if (file_exists($zipArchivePath) === false) {
            error_log("Zip Archive Utility Error: Failed to extract file {$fileName} from {$zipArchivePath} because the specified archive does not exist.");
            return false;
        }
        if ($this->zipHasFile($zipArchivePath, $fileName) === false) {
            error_log("Zip Archive Utility Error: Failed to extract file {$fileName} from {$zipArchivePath} because the specified file does not exist in the archive.");
            return false;
        }
        if ($this->open($zipArchivePath) !== true) {
            error_log("Zip Archive Utility Error: Failed to extract file {$fileName} from {$zipArchivePath} to {$extractionPath} because the archive could not be opened. Zip Archive Error: " . $this->getStatusString());
            return false;
        }
        if (($status = $this->extractTo($extractionPath, $fileName)) !== true) {
            error_log("Zip Archive Utility Error: Failed to extract file {$fileName} from {$zipArchivePath} to {$extractionPath}. Extraction failed. Zip Archive Error: " . $this->getStatusString());
        }
        $this->close();
        return $status;
    }

    /**
     * Determines if the specified file exists in the specified zip.
     * @param string $zipArchivePath The path to the zip archive to search in.
     * @param string $fileName The name of the file to search for.
     * @return bool True if the specified file exists in the specified zip, false otherwise.
     */
    public function zipHasFile(string $zipArchivePath, string $fileName): bool
    {
        if (!empty($this->readFileFromZip($zipArchivePath, $fileName))) {
            return true;
        }
        return false;
    }

    /**
     * Read the contents of a file from the specified zip archive.
     *
     * Note: This method will return an empty string if the specified file could not be read.
     *
     * Note: Since this method will return an empty string if the file cannot be read,
     * or if the file is empty, it is not reliable to check if this method returns
     * an empty string to check if this method was able to successfully read the file
     * from the zip archive.
     *
     * @param string $zipArchivePath The path to the zip archive that contains the file to be read.
     * @param string $fileName The name of the file to read.
     * @return string The file's contents, or an empty string.
     */
    public function readFileFromZip(string $zipArchivePath, string $fileName): string
    {
        if (file_exists($zipArchivePath) !== true) {
            error_log("Zip Archive Utility Error: Could not read {$fileName} from {$zipArchivePath} archive because the specified archive does not exist.");
            return '';
        }
        if ($this->open($zipArchivePath) !== true || $this->getFromName($fileName) === false) {
            error_log("Zip Archive Utility Error: Could not read {$fileName} from $zipArchivePath because the file does not exist in the specified archive.");
            return '';
        }
        return $this->getFromName($fileName);
    }
}
