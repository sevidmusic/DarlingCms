<?php namespace DarlingCms\abstractions\crud;

/**
 * Class Acrud. Defines an abstract implementation of the \DarlingCms\interfaces\crud\Icrud interface.
 * This implementation's create(), read(), update(), and delete() methods base their logic
 * on the implementation of the the abstract methods pack(), unpack(), and query().
 *
 * @package DarlingCms\abstractions\crud
 */
abstract class Acrud implements \DarlingCms\interfaces\crud\Icrud
{
    /**
     * Read data from storage.
     * @param string $storageId The data's storage id.
     * @return mixed The data, or false on failure.
     */
    final public function read(string $storageId)
    {
        return $this->unpack($this->query($storageId, 'load'));
    }

    /**
     * Unpack data packed by the pack() method.
     *
     * @param mixed $packedData The packed data.
     *
     * @return mixed The unpacked data, or false on failure.
     */
    abstract protected function unpack($packedData);

    /**
     * Query stored data. This method is the backbone of all implementations of this abstract class.
     * It is through the implementation of this method that the create(), read(), update(), and delete()
     * methods are able to run queries on stored data.
     *
     * @param string $storageId The storage id of the data to query.
     *
     * @param string $mode The query mode determines what kind of query is run. There are
     *                     three query modes(*) that all implementations of this method must
     *                     accommodate: save, load, and delete.
     *
     *                     (*) Note: Implementations may implement additional query modes but the
     *                               following three query modes: save, load, and delete; must
     *                               always be implemented.
     *
     *                     --- Query Modes ---
     *
     *                     'save':  This mode is responsible for storing data. It expects
     *                                the $data parameter to be the data that is to be stored.
     *
     *                     'load': This mode reads data from storage.
     *
     *                     'delete': This mode deletes stored data.
     *
     * @param mixed $data (optional) The data that is to be stored. Only required if query $mode is set to 'save'.
     *
     * @return mixed|bool Should return the result of the query, or false on failure.
     */
    abstract protected function query(string $storageId, string $mode, $data = null);

    /**
     * Updates stored data.
     * @param string $storageId The storage id of the data to update.
     * @param mixed $newData The new data.
     * @return bool True if data was updated, false otherwise.
     */
    final public function update(string $storageId, $newData)
    {
        if ($this->delete($storageId) === true) {
            return $this->create($storageId, $newData);
        }
        return false;
    }

    /**
     * Delete data from storage.
     * @param string $storageId The storage id of the data to delete.
     * @return bool True if data was deleted, false otherwise.
     */
    final public function delete(string $storageId)
    {
        return $this->query($storageId, 'delete');
    }

    /**
     * Store new data.
     * @param string $storageId An id to be assigned to the stored data.
     * @param mixed $data The data to store.
     * @return bool Return true if data was stored, false otherwise.
     */
    final public function create(string $storageId, $data)
    {
        return $this->query($storageId, 'save', $this->pack($data));
    }

    /**
     * Pack data for storage.
     * @param mixed $data The data to be packed.
     * @return bool True if data was packed successfully, false otherwise.
     */
    abstract protected function pack($data);

}
