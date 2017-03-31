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
        $this->storagePath = trim(str_replace('core/classes/crud', '', __DIR__) . '.dcms/');
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
                return (file_put_contents($pathToJsonFile, $data, LOCK_EX) > 0);
            case 'load':
                if (file_exists($pathToJsonFile) === true) {
                    return file_get_contents($pathToJsonFile);
                }
                return false;
            case 'delete':
                return unlink($pathToJsonFile);
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
}
