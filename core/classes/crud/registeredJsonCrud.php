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
        $storageDir = $this->determineStorageDirectory();
        /* */
        if (is_dir($storageDir) === false) {
            if (mkdir($storageDir, 0755, false) === false) {
                return false;
            }
        }
        $this->storagePath = $storageDir . '/';
        return isset($this->storagePath);
    }

    private function determineStorageDirectory()
    {
        return trim(str_replace('core/classes/crud', '', __DIR__) . '.dcms');
    }

    /**
     * Returns the full storage path to where data is stored.
     * @return string The storage path.
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    public function getRegistryData(string $storageId, string $name = '*')
    {
        // TODO: Implement getRegistryData() method.
    }

    /**
     * @inheritdoc
     */
    protected function query(string $storageId, string $mode, $data = null)
    {
        $pathToJsonFile = $this->storagePath . $this->safeId($storageId) . '.json';
        switch ($mode) {
            case 'save':
                /* Attempt to save the data. */
                if (file_put_contents($pathToJsonFile, $data, LOCK_EX) > 0) {
                    /* Return true if data was saved, false otherwise. */
                    return true;
                }
                /* Return false if data was not saved. */
                return false;
            case 'load':
                if (file_exists($pathToJsonFile) === true) {
                    return file_get_contents($pathToJsonFile);
                }
                return false;
            case 'delete':
                if (file_exists($pathToJsonFile) === true) {
                    return unlink($pathToJsonFile);
                }
                return false;
            default:
                error_log('Attempt to use invalid query mode in ' . __FILE__);
                return false;
        }
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
            'storageDirectory' => str_replace('\\', '/', $classification),
            'storageExtension' => '.json',
            'classification' => $classification,
            'modified' => time(),
        );
    }

}
