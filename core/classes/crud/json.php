<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/30/17
 * Time: 5:49 PM
 */

namespace DarlingCms\classes\crud;

/**
 * Class json. Implementation of the \DarlingCms\abstractions\crud\Acrud() abstract
 * class that uses JSON files as it's storage mechanism.
 * @package DarlingCms\classes\crud
 */
class json extends \DarlingCms\abstractions\crud\Acrud
{
    private $storagePath;

    /**
     * json constructor. Initializes the $storagePath.
     */
    public function __construct()
    {
        /* Initialize storage path. */
        $this->initializeStoragePath();
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
                    if (unlink($pathToJsonFile) === true) {
                        return true;
                    }
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
        return hash_hmac('sha256', $storageId, 'ads9fuj4ih98fyhudihf908ydfgv2goe7r87gwe');
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

}
