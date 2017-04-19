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
    private $registry;

    /**
     * json constructor. Initializes the $storagePath, and the $registry.
     */
    public function __construct()
    {
        /* Initialize storage path. */
        $this->initializeStoragePath();
        /* Initialize the internal registry. */
        $this->initializeRegistry();
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
                    /* Do not register the registry, it's initialization and registration
                       are handled by the initializeRegistry() method. */
                    if ($storageId !== 'registry') {
                        /* Return true if data was saved and registered, false otherwise. */
                        return $this->register($storageId, $data);
                    }
                    /* When saving the registry, return true as long as data was saved, false otherwise. */
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
                        /* Do not ever un-register the registry. */
                        if ($storageId !== 'registry') {
                            $this->unRegister($storageId);
                        }
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
     * Initializes the registry.
     * @return bool True if registry was initialized, false otherwise.
     */
    private function initializeRegistry()
    {
        /* Check if the stored registry exists. */
        if ($this->read('registry') === false) {
            /* If the stored registry does not exist, create it. */
            $storageId = 'registry';
            $this->create('registry', array(
                $storageId => $this->generateRegistryData($storageId, 'array'),
            ));
        }
        /* Sync the internal and stored registries. */
        $this->registry = $this->read('registry');
        return isset($this->registry);
    }

    /**
     * Register data in the registry.
     *
     * @return bool True if data was registered and registry was updated, false otherwise.
     */
    private function register(string $storageId, string $data)
    {
        /* Register data. */
        $this->registry[$storageId] = $this->generateRegistryData($storageId, $this->classify($data));
        if (isset($this->registry[$storageId]) === true) {
            return $this->update('registry', $this->registry);
        }
        /* Return false if data was not registered. */
        return false;
    }

    /**
     * Remove data from the registry.
     *
     * @return bool True if data was un-registered and registry was updated, false otherwise.
     */
    private function unRegister(string $storageId)
    {
        /* Un-register data. */
        unset($this->registry[$storageId]);
        /* If data was un-registered, update the stored registry. */
        if (isset($this->registry[$storageId]) === false) {
            return $this->update('registry', $this->registry);
        }
        /* Return false if data was not registered. */
        return false;
    }

    private function generateRegistryData(string $storageId, string $classification)
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

    /**
     * Returns the registry as an associative array where keys are storage ids and values are safe ids.
     * @return mixed The registry as an associative array.
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Determines the type or class of a piece of packed data.
     * @param string $data The packed data to classify.
     * @return string The classification.
     */
    private function classify(string $data)
    {
        $classification = gettype($this->unpack($data));
        if ($classification === 'object') {
            return get_class($this->unpack($data));
        }
        return $classification;
    }

}
