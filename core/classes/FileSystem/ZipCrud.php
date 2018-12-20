<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 11/3/18
 * Time: 3:11 PM
 */

namespace DarlingCms\classes\FileSystem;

/**
 * Class ZipCrud. This class provides methods to interface with zip files.
 * @package DarlingCms\classes\FileSystem
 * @see ZipCrud::zip()
 * @see ZipCrud::unzip()
 */
class ZipCrud extends \ZipArchive
{
    /**
     * Create a zip archive of the specified path, and on success, deletes the original item.
     * WARNING: If zip is successful, the original item will be deleted!
     * @todo: Implement parameter $deleteOriginal which will accept a bool that will determine whether or
     *        or not the original item should be deleted, i.e., make deleting the original optional.
     * @param string $targetPath The path to the item to zip.
     * @return bool True if the item was zipped, and the original item was deleted, false otherwise.
     */
    public function zip(string $targetPath): bool
    {
        $zipFilePath = $targetPath . '.zip';
        if (extension_loaded('zip') === false) {
            error_log('Zip Crud Error: Zip extension does not exist.');
            return false;
        }
        if (file_exists($targetPath) === false) {
            error_log('Zip Crud Error: Failed to archive ' . $targetPath . ' because no file or directory exists at that path.');
            return false;
        }
        if (file_exists($zipFilePath) === true) {
            error_log('Zip Crud Error: Failed to archive ' . $targetPath . '. An archive at ' . $zipFilePath . ' already exists.');
            return false;
        }
        if ($archiveError = $this->open($zipFilePath, \ZipArchive::CREATE) !== true) {
            error_log('Zip Crud Error: Failed to archive ' . $targetPath . '. Failed to open archive at ' . $zipFilePath . '. ZipArchive Error Code: ' . $archiveError);
            return false;
        }
        switch (is_dir($targetPath)) {
            case true:
                // handle dir
                $this->addSubDir($targetPath);
                break;
            case false:
                // handle files
                $this->addFile($targetPath, basename($targetPath));
                break;
        }
        if ($this->getStatusString() !== 'No error') {
            error_log('Zip Crud Error: Failed to archive ' . $targetPath . '. ZipArchive Error "' . $this->getStatusString() . '"');
            $this->close();
            unlink($zipFilePath);
            return false;
        }
        $this->close();
        if ($this->removeDir($targetPath) !== true) {
            error_log('Zip Crud Error: Failed to remove original item ' . $targetPath);
            return false;

        }
        if (file_exists($zipFilePath) === false) {
            error_log('Zip Crud Error: Failed to archive ' . $targetPath);
            return false;
        }
        return true;
    }

    private function addSubDir(string $targetPath)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetPath), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                continue;
            $file = realpath($file);
            if (is_dir($file) === true) {
                $this->addEmptyDir(str_replace($targetPath . '/', '', $file . '/'));
            } else if (is_file($file) === true) {
                $this->addFromString(str_replace($targetPath . '/', '', $file), file_get_contents($file));
            }
        }
    }

    /**
     * @param $path
     * @return bool
     * @todo ! Resolve partial delete on internal error. If this method fails, any items successfully removed prior to failure should be restored!!!
     * @todo: Move this method into the FileCrud class once it is created.
     */
    public static function removeDir($path): bool
    {
        foreach ($scanDir = array_diff(scandir($path), array('.', '..')) as $item) {
            $pathToItem = $path . '/' . $item;
            if (is_file($pathToItem) === true) {
                if (unlink($pathToItem) === false) {
                    error_log('ZipCrud error: Failed to remove file ' . $pathToItem);
                }
                continue;
            }
            if (is_dir($pathToItem) === true) {
                if (self::removeDir($pathToItem) === false) {
                    error_log('ZipCrud error: Failed to empty directory  ' . $pathToItem);
                }
            }
            continue;
        }
        if (($status = rmdir($path)) === false) {
            error_log('ZipCrud error: Failed to remove directory  ' . $path);
        }
        return $status;
    }

    /**
     * Unzip an archive at a specified path, and on success delete the original archive.
     * @param string $zipFilePath The absolute path to the zip archive to be unzipped.
     * @return bool True if specified zip archive was unzipped, false otherwise.
     */
    public function unzip(string $zipFilePath): bool
    {
        $extractionPath = str_replace('.zip', '', $zipFilePath);
        if (file_exists($extractionPath) === true) {
            error_log('Zip Crud Error: Failed to un-archive ' . $zipFilePath . ' because ' . $extractionPath . ' already exists.');
            return false;
        }
        if (file_exists($zipFilePath) === true) {
            $this->open($zipFilePath);
            if ($this->extractTo($extractionPath) === false) {
                error_log('Zip Crud Error: Failed to un-archive ' . $zipFilePath . '. Extraction to ' . $extractionPath . ' failed. Zip Archive Error: ' . $this->getStatusString());
                $this->close();
                return false;
            }
            $this->close();
            unlink($zipFilePath);
            return file_exists($zipFilePath) === false;
        }
        return false;
    }

    public function zipHasFile(string $zipFilePath, string $fileName): bool
    {
        if (file_exists($zipFilePath) !== true) {
            error_log("Zip Crud Error: Could not determine if file {$fileName} exists in the {$zipFilePath} archive because no such archive exists.");
            return false;
        }
        if ($this->open($zipFilePath) === TRUE && $this->locateName($fileName) !== false) {
            return true;
        }
        return false;
    }

    public function zipHasDir(string $zipFilePath, string $dirName): bool
    {
        if (file_exists($zipFilePath) !== true) {
            error_log("Zip Crud Error: Could not determine if directory {$dirName} exists in the {$zipFilePath} archive because no such archive exists.");
            return false;
        }
        if ($this->open($zipFilePath) === TRUE && $this->locateName($dirName . '/') !== false) {
            return true;
        }
        return false;
    }

    public function readFileFromZip(string $zipFilePath, string $fileName): string
    {
        if (file_exists($zipFilePath) !== true) {
            error_log("Zip Crud Error: Could not read {$fileName} from {$zipFilePath} archive because the specified archive does not exist.");
            return '';
        }
        if ($this->zipHasFile($zipFilePath, $fileName) === false) {
            error_log("Zip Crud Error: Could not read {$fileName} from $zipFilePath because the file does not exist in the specified archive.");
            return '';
        }
        if ($this->open($zipFilePath)) {
            return $this->getFromName($fileName);
        }
        return '';
    }

    public function extractFileFromZip(string $zipFilePath, string $extractionPath, string $fileName, bool $overwrite = false): bool
    {
        if ($overwrite === false && file_exists($extractionPath . '/' . $fileName) === true) {
            error_log("Zip Crud Error: Failed to extract file {$fileName} from {$zipFilePath} because the file {$extractionPath}/{$fileName} already exists.");
            return false;
        }
        if (file_exists($zipFilePath) === false) {
            error_log("Zip Crud Error: Failed to extract file {$fileName} from {$zipFilePath} because the specified archive does not exist.");
            return false;
        }
        if ($this->zipHasFile($zipFilePath, $fileName) === false) {
            error_log("Zip Crud Error: Failed to extract file {$fileName} from {$zipFilePath} because the specified file does not exist in the archive.");
            return false;
        }
        if ($this->open($zipFilePath) !== true) {
            error_log("Zip Crud Error: Failed to extract file {$fileName} from {$zipFilePath} to {$extractionPath} because the archive could not be opened. Zip Archive Error: " . $this->getStatusString());
            return false;
        }
        if (($status = $this->extractTo($extractionPath, $fileName)) !== true) {
            error_log("Zip Crud Error: Failed to extract file {$fileName} from {$zipFilePath} to {$extractionPath}. Extraction failed. Zip Archive Error: " . $this->getStatusString());
        }
        $this->close();
        return $status;
    }
    // @todo : Implement the following methods:
    // extractDirFromZip(string $dirName, string $extractionPath):bool {} // extract a specified directory from the zip archive. NOTE: Directories should be extracted to the same directory the archive is in to protect the file system.
}
