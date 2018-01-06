<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 1/5/18
 * Time: 6:27 PM
 */

namespace DarlingCms\classes\crud;

/**
 * Class session. Provides a common gateway for session interaction. This class can be used
 * to create, read, update, and delete data from the current session.
 *
 * @package DarlingCms\classes\crud
 */
class session extends \DarlingCms\abstractions\crud\Acrud
{
    /**
     * Query stored data.
     * @inheritdoc
     * @return mixed|bool Should return the result of the query, or false on failure.
     */
    protected function query(string $storageId, string $mode, $data = null)
    {
        switch ($mode) {
            case 'save':
                session_start();
                $_SESSION[$storageId] = $data;
                $status = isset($_SESSION[$storageId]);
                session_write_close();
                return $status;
                break;
            case 'load':
                session_start(array('read_and_close' => true));
                return (isset($_SESSION[$storageId]) === true ? $_SESSION[$storageId] : false);
                break;
            case 'delete':
                session_start();
                unset($_SESSION[$storageId]);
                $status = !isset($_SESSION[$storageId]);
                session_write_close();
                return $status;
                break;
        }
        return false;
    }

    /**
     * Pack data for storage.
     * @param mixed $data The data to be packed.
     * @return bool True if data was packed successfully, false otherwise.
     */
    protected function pack($data)
    {
        return $data;
    }

    /**
     * Unpack data packed by the pack() method.
     *
     * @param mixed $packedData The packed data.
     *
     * @return mixed The unpacked data, or false on failure.
     */
    protected function unpack($packedData)
    {
        return $packedData;
    }

}
