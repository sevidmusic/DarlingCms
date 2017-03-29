<?php namespace DarlingCms\interfaces\crud;

/**
 * Interface Icrud. Defines the interface for an object whose responsibility is
 * to manage stored data.
 *
 * @package DarlingCms\interfaces\crud
 *
 */

interface Icrud
{
    /**
     * Store the $data under a unique storage id.
     *
     * @param string $storageId A unique storage id to store the data under.
     *
     * @param mixed $data The data to store.
     *
     * @return bool True if data was stored successfully, false otherwise.
     */
    public function create(string $storageId, $data);

    /**
     * Read data from storage.
     *
     * @param string $storageId The the storage id of the data to read.
     *
     * @return mixed The data.
     */
    public function read(string $storageId);

    /**
     * Update stored data.
     *
     * @param string $storageId The storage id of the data to be updated.
     *
     * @param mixed $newData The new data.
     *
     * @return bool True if update was successful, false otherwise.
     */
    public function update(string $storageId, $newData);

    /**
     * Delete data from storage.
     *
     * @param string $storageId The the data's storage id.
     *
     * @return bool True if data was deleted, false otherwise.
     */
    public function delete(string $storageId);
}
