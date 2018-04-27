<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 10:32 AM
 */

namespace DarlingCms\interfaces\crud;

/**
 * Interface ICrud. Defines the contract of an object that can create, read, update, and delete data associated
 * with an id.
 * @package DarlingCms\interfaces\crud
 * @see ICrud::create()
 * @see ICrud::read()
 * @see ICrud::update()
 * @see ICrud::delete()
 */
interface ICrud
{
    /**
     * Create data associated with an id.
     * @param string $dataId The id to associate with the data.
     * @param mixed $data The data.
     * @return bool True if data was created successfully, false otherwise.
     */
    public function create(string $dataId, $data): bool;

    /**
     * Read data associated with an id.
     * @param string $dataId The id associated with the data to be read.
     * @return mixed The data.
     */
    public function read(string $dataId);

    /**
     * Update data associated with an id.
     * @param string $dataId The id associated with the data to be updated.
     * @param mixed $newData The new data.
     * @return bool True if update was successful, false otherwise.
     */
    public function update(string $dataId, $newData): bool;

    /**
     * Delete data associated with an id.
     * @param string $dataId The id associated with the data to be deleted.
     * @return bool True if data was deleted, false otherwise.
     */
    public function delete(string $dataId): bool;
}
