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
        $this->storagePath = trim(str_replace('core/classes/crud', '', __DIR__) . '.dcms/');
        /* Make sure the stored registry exists. */
        if ($this->read('registry') === false) {
            /* If the stored registry does not exist, create it. */
            $this->create('registry', array('registry' => $this->safeId('registry')));
        }
        /* Initialize $registry using stored registry. */
        $this->registry = $this->read('registry');
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
     * Returns the registry as an associative array where keys are storage ids and values are safe ids.
     * @return mixed The registry as an associative array.
     */
    public function getRegistry()
    {
        return $this->read('registry');
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
    protected function unpack($packedData)
    {
        return unserialize(base64_decode(json_decode($packedData)));
    }

    /**
     * @inheritdoc
     */
    protected function query(string $storageId, string $mode, $data = null)
    {
        $pathToJsonFile = $this->storagePath . $this->safeId($storageId) . '.json';
        switch ($mode) {
            case 'save':
                $this->registry[$storageId] = $this->safeId($storageId);
                $this->updateRegistry($storageId);
                return (file_put_contents($pathToJsonFile, $data, LOCK_EX) > 0);
            case 'load':
                if (file_exists($pathToJsonFile) === true) {
                    return file_get_contents($pathToJsonFile);
                }
                return false;
            case 'delete':
                unset($this->registry[$storageId]);
                $this->updateRegistry($storageId);
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
     * Update the registry.
     * @param string $storageId The $storageId of the data currently being processed.
     * @return bool True if registry was updated, false otherwise.
     */
    private function updateRegistry(string $storageId)
    {
        $status = false;
        if ($storageId !== 'registry') {
            $status = $this->update('registry', $this->registry);
        }
        $this->reloadRegistry();
        return $status;
    }

    /**
     * Re-load the registry into the $registry property. This method is called whenever
     * the registry is updated, i.e., whenever updateRegistry() is called.
     * @return bool True if registry was re-loaded, false otherwise.
     */
    private function reloadRegistry()
    {
        $origRegistry = $this->registry;
        unset($this->registry);
        $this->registry = $this->read('registry');
        return ($this->registry !== $origRegistry);
    }

    /**
     * @inheritdoc
     */
    protected function pack($data)
    {
        return json_encode(base64_encode(serialize($data)));
    }

}
