<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 6/2/18
 * Time: 12:05 AM
 */

namespace DarlingCms\classes\crud;

use DarlingCms\interfaces\crud\ICrud;

/**
 * Class SessionCrud. Defines an implementation of the ICrud interface that is responsible for creating, reading,
 * updating, and deleting data from the current session.
 * @package DarlingCms\classes\crud
 * @see SessionCrud::create()
 * @see SessionCrud::read()
 * @see SessionCrud::update()
 * @see SessionCrud::delete()
 */
class SessionCrud implements ICrud
{
    /**
     * Create data in the current session.
     * @param string $dataId The index to associate the data with in the current session.
     * @param mixed $data The data.
     * @return bool True if data was successfully stored in the current session, false otherwise.
     */
    public function create(string $dataId, $data): bool
    {
        session_start();
        $_SESSION[$dataId] = $data;
        $status = isset($_SESSION[$dataId]);
        session_write_close();
        return $status;
    }

    /**
     * Read data associated with an id from the current session.
     * Note: null is returned if the data does not exist in the session, or the data has the value null.
     * @param string $dataId The index associated with the data to be read from the current session.
     * @return mixed The data.
     */
    public function read(string $dataId)
    {
        session_start(array('read_and_close' => true));
        return (isset($_SESSION[$dataId]) === true ? $_SESSION[$dataId] : null);
    }

    /**
     * Update data associated in the current session.
     * @param string $dataId The index associated with the data to be updated in the current session.
     * @param mixed $newData The new data.
     * @return bool True if update was successful, false otherwise.
     */
    public function update(string $dataId, $newData): bool
    {
        return $this->create($dataId, $newData);
    }

    /**
     * Delete data from the current session.
     * @param string $dataId The index associated with the data to be deleted from the current session.
     * @return bool True if data was deleted from the current session, false otherwise.
     */
    public function delete(string $dataId): bool
    {
        session_start();
        unset($_SESSION[$dataId]);
        $status = !isset($_SESSION[$dataId]);
        session_write_close();
        return $status;
    }
}
