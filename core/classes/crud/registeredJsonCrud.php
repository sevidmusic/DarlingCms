<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/20/17
 * Time: 12:23 PM
 */

namespace DarlingCms\classes\crud;


class registeredJsonCrud extends \DarlingCms\abstractions\crud\AregisteredCrud
{
    private $storagePath;

    /**
     * json constructor. Initializes the $storagePath, and the $registry.
     */
    public function __construct()
    {
        /* Initialize storage path. */
        $this->initializeStoragePath();
        /* Call parent __construct() */
        parent::__construct();
    }

    /**
     * Initializes the storage path.
     * @return bool True if storage path was initialized, false otherwise.
     */
    private function initializeStoragePath()
    {
        /* Determine the storage directory. */
        $storageDir = trim(str_replace('core/classes/crud', '', __DIR__) . '.dcms');
        /* */
        if (is_dir($storageDir) === false) {
            if (mkdir($storageDir, 0755, false) === false) {
                return false;
            }
        }
        $this->storagePath = $storageDir . '/';
        return isset($this->storagePath);
    }

    /**
     * Gets registry data associated with a specified storage id.
     * @inheritdoc
     */
    public function getRegistryData(string $storageId, string $name = '*')
    {
        /* Attempt to read the registry. */
        if (($registry = $this->read('registry')) !== false) {
            /* Get the array of registry data associated with the specified storage id.  */
            if (isset($registry[$storageId]) && is_array($registry[$storageId])) {
                /* If the $name parameter is set to the wildcard character "*", return the entire array of
                     registry data associated with the specified storage id. */
                if ($name === '*') {
                    return $registry[$storageId];
                }
                /* If the specific registry data indicated by the $name parameter exists in the
                   registry data array associated with the specified storage id, return it. */
                if (isset($registry[$storageId][$name]) === true) {
                    return $registry[$storageId][$name];
                }
            }
        }
        /* Return false if registry does not exist, if there is no registry data for the specified storage id,
           or if the registry data indicated by the $name parameter does not exist in the registry data for the
           specified storage id. */
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function query(string $storageId, string $mode, $data = null)
    {
        switch ($mode) {
            case 'save':
                $storageDirectoryPath = $this->generateStorageDirectoryPath($this->classify($data));
                $savePath = $this->generateSavePath($storageId, $this->classify($data));
                /* Make sure the appropriate storage directory exists for the data. */
                if (is_dir($storageDirectoryPath) === false) {
                    mkdir($storageDirectoryPath, 0755, true);
                }

                /* Attempt to save the data. */
                if (file_put_contents($savePath, $data, LOCK_EX) > 0) {
                    /* Return true if data was saved, false otherwise. */
                    return true;
                }
                /* Return false if data was not saved. */
                return false;
            case 'load':
                /* Determine the save path based on the registry data for the specified storage id. */
                $savePath = $this->determineSavePath($storageId);
                if (file_exists($savePath) === true) {
                    return file_get_contents($savePath);
                }
                /* Return false if the storage directory or the file associated with specified
                   storage id does not exist.*/
                return false;
            case 'delete':
                /* Determine the save path based on the registry data for the specified storage id. */
                $savePath = $this->determineSavePath($storageId);
                if (file_exists($savePath) === true) {
                    return unlink($savePath);
                }
                return false;
            default:
                error_log('Attempt to use invalid query mode in ' . __FILE__);
                return false;
        }
    }

    private function generateStorageDirectoryPath(string $classification)
    {
        return $this->storagePath . str_replace('\\', '/', $classification) . '/';
    }

    /**
     * Generates a save path for the data based on it's classification.
     * @param string $storageId The storage id of the data to generate a save path for.
     * @param string $classification The classification of the data to generate a save path for.
     * @return string The generated save path.
     */
    private function generateSavePath(string $storageId, string $classification)
    {
        return $this->generateStorageDirectoryPath($classification) . $this->safeId($storageId) . '.json';

    }

    /**
     * Generates an id that is safe to use for storage from the $storageId.
     * @param string $storageId The storage id to generate a safe id for.
     * @return string An id generated from the $storageId that is safe to use for storage.
     */
    private function safeId(string $storageId)
    {
        return hash_hmac('sha256', $storageId, 'sdfghu7654esdfghbvcdsw3456yhgbnju765432345rtfg', false);
    }

    /**
     * Determines the save path for the specified storage id based
     * on the registry data associated with the specified storage id.
     * @param string $storageId The storage id to determine a save path for.
     * @return bool|string The save path, or false if the save path could not be determined.
     */
    private function determineSavePath(string $storageId)
    {
        /* Lookup the storage directory for the specified storage id in the registry. */
        switch ($storageId) {
            case 'registry':
                $storageDirectoryPath = $this->getStoragePath() . 'array/';
                break;
            default:
                if (isset($this->getRegistry()[$storageId]['storageDirectory']) === false) {
                    return false;
                }
                $storageDirectoryPath = $this->getRegistry()[$storageId]['storageDirectory'];
                break;
        }
        if (is_dir($storageDirectoryPath) === true) {
            return $storageDirectoryPath . $this->safeId($storageId) . '.json';
        }
        return false;
    }

    /**
     * Returns the full storage path to where data is stored.
     * @return string The storage path.
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * @inheritdoc
     */
    protected function pack($data)
    {
        return json_encode(base64_encode(serialize($data)));
    }

    /**
     * @inheritdoc
     */
    protected function unpack($packedData)
    {
        return unserialize(base64_decode(json_decode($packedData), false));
    }

    protected function generateRegistryData(string $storageId, string $classification, array $additionalData = array())
    {
        return array(
            'storageId' => $storageId,
            'safeId' => $this->safeId($storageId),
            'storageDirectory' => $this->generateStorageDirectoryPath($classification),
            'storageExtension' => '.json',
            'classification' => $classification,
            'modified' => time(),
            'additionalData' => $additionalData,
        );
    }

}
