<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/19/17
 * Time: 6:00 PM
 */

namespace DarlingCms\abstractions\crud;

/**
 * Class AregisteredCrud. Defines the basic contract of a C.R.U.D. object that uses a registry to interact with
 * stored data.
 * @package DarlingCms\abstractions\crud
 * @see AregisteredCrud::create()
 * @see AregisteredCrud::read()
 * @see AregisteredCrud::update()
 * @see AregisteredCrud::delete()
 * @see AregisteredCrud::getRegistry()
 * @see AregisteredCrud::getRegistryData()
 *
 */
abstract class AregisteredCrud implements \DarlingCms\interfaces\crud\Icrud
{
    /**
     * @var string The registry's storage id.
     */
    const REGISTRY_ID = 'registry';

    /**
     * @var array $registry An array of registered stored data. (Defaults to an empty array)
     */
    protected $registry = array();

    /**
     * This property prevents an infinite recursion loop when syncing the registry. This property will be
     * set to true by the syncRegistry() method when it's called, which will then set it back to false once
     * sync is complete. The potential for an infinite recursion loop arises because the syncRegistry() method
     * uses the read() method to sync the $registry property with the stored registry, and the read() method in
     * turn needs to call syncRegistry() to insure the registry is up to date before performing a read query. This
     * property insures that read() only calls syncRegistry() if syncing is not in process.
     * @var bool True if syncing the registry, false otherwise. This property's value is managed by the
     *           syncRegistry() method.
     * @see AregisteredCrud::syncRegistry()
     * @see AregisteredCrud::read()
     */
    private $syncing = false;

    /**
     * AregisteredCrud constructor. Initializes the registry when necessary.
     * @see AregisteredCrud::initializeRegistry()
     */
    public function __construct()
    {
        /* Initialize the registry. */
        $this->initializeRegistry();
    }

    /**
     * Initializes the registry.
     * @return bool True if registry was initialized, false otherwise.
     * @see AregisteredCrud::read()
     * @see AregisteredCrud::create()
     * @see AregisteredCrud::generateRegistryData()
     */
    private function initializeRegistry(): bool
    {
        /* Check if the stored registry exists, and that the $registry property is not an empty array. */
        if ($this->read(self::REGISTRY_ID) === false || empty($this->registry)) {
            /* If the stored registry does not exist, or the $registry property is an empty array, generate
               the registry data for the registry and store it. */
            $this->create(self::REGISTRY_ID, array(
                self::REGISTRY_ID => $this->generateRegistryData(self::REGISTRY_ID, 'array'),
            ));
        }
        /* Sync the $registry property with the stored registry, returning true on success, false otherwise. */
        return $this->syncRegistry();
    }

    /**
     * Read data from storage.
     * @param string $storageId The data's storage id.
     * @param string $classification (optional) If set, only data whose classification matches
     *                                          the specified classification will be returned.
     *                                          i.e., if set, even if the storage id matches
     *                                          a piece of stored data, the data will only be
     *                                          returned if it's classification matches the
     *                                          specified classification. Stored data is
     *                                          classified by type, except for objects, which
     *                                          are classified by their fully qualified namespace
     *                                          including the class name.
     * @return mixed The data, or false on failure.
     * Note: Since this crud implementation accommodates saving of any type of data,
     * including boolean values, it is possible that read() will return false if
     * the data associated with the specified storage id is the boolean false.
     * @see AregisteredCrud::syncRegistry()
     * @see AregisteredCrud::unpack()
     * @see AregisteredCrud::query()
     * @see AregisteredCrud::classify()
     */
    final public function read(string $storageId, string $classification = '')
    {
        /**
         * Only call syncRegistry() if the $syncing property is set to false, calling syncRegistry() from the read()
         * method when syncing is happening would result in an infinite recursion loop because syncRegistry() uses
         * the read() method to sync the $registry property with the stored registry.
         */
        if ($this->syncing === false) {
            /* Sync registry to make sure it is up to date with any changes that may have been made by another
               instance of the same implementation of this class. */
            $this->syncRegistry();
        }
        /* Check if a classification was specified. */
        if ($classification !== '') {
            /* Only return data if the classification of the requested data matches the specified classification. */
            if (isset($this->registry[$storageId]) === true && $this->registry[$storageId]['classification'] === $classification) {
                /* Return the data. */
                return $this->unpack($this->query($storageId, 'load'));
            }
            /* The storage id was valid, but the data associated does not match the specified classification
               return false. */
            return false;
        }
        /* Return the data, or false on failure. */
        return $this->unpack($this->query($storageId, 'load'));
    }

    /**
     * Unpack data packed by the pack() method.
     * @param mixed $packedData The packed data.
     * @return mixed The unpacked data, or false on failure.
     * Note: Since this crud implementation accommodates saving any type of data,
     * including boolean values, it is possible that unpack() will return false if
     * the data being unpacked is the boolean false.
     */
    abstract protected function unpack($packedData);

    /**
     * Query stored data. This method is the backbone of all implementations of this class.
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
     *                     'save':  This mode is responsible for storing data associated with the specified
     *                              $storageId. It expects the $data parameter to be the data that is to be stored
     *                              under the specified $storageId.
     *
     *                     'load': This mode reads data associated with the specified $storageId from storage.
     *
     *                     'delete': This mode deletes data associated with the specified $storageId from storage.
     *
     * @param mixed $data (optional) The data that is to be stored. Only required if query $mode is set to 'save'.
     *
     * @return mixed|bool Should return the result of the query, or false on failure.
     * Note: Since this crud implementation accommodates saving any type of data,
     * including boolean values, it is possible that query($storageId, 'load') will return false if
     * the data associated with the specified $storageId is the boolean false.
     */
    abstract protected function query(string $storageId, string $mode, $data = null);

    /**
     * Store new data.
     * @param string $storageId An id to be assigned to the stored data.
     * @param mixed $data The data to store.
     * @return bool Return true if data was stored, false otherwise.
     * @see AregisteredCrud::syncRegistry()
     * @see AregisteredCrud::pack()
     * @see AregisteredCrud::query()
     * @see AregisteredCrud::register()
     */
    final public function create(string $storageId, $data): bool
    {
        /* Sync registry to make sure it is up to date with any changes that may have been made by another
           instance of the same implementation of this class. */
        $this->syncRegistry();
        /* Pack the data for storage. */
        $packedData = $this->pack($data);
        /* Run a save query on the packed data. */
        if ($this->query($storageId, 'save', $packedData) !== false) {
            /* Never register the registry, the registry is managed by the registry methods. */
            if ($storageId !== self::REGISTRY_ID) {
                /* Register newly stored data in the registry. */
                return $this->register($storageId, $packedData);
            }
            /* When handling the registry, return true as long the save query was successful. */
            return true;
        }
        /* Return false if save query failed. */
        return false;
    }

    /**
     * Pack data for storage.
     * @param mixed $data The data to be packed.
     * @return mixed The packed data.
     * Note: Implementations that do not need to "pack" data before storage can simply return the $data
     * as is from the implementation of this method, i.e., return $data.
     */
    abstract protected function pack($data);

    /**
     * Register data in the registry.
     * @param string $storageId The storage id of the data to register in the registry.
     * @param mixed $data The data to register.
     * @return bool True if data was registered and registry was updated, false otherwise.
     * @see AregisteredCrud::generateRegistryData()
     * @see AregisteredCrud::classify()
     * @see AregisteredCrud::update()
     */
    private function register(string $storageId, $data): bool
    {
        /* Register the data in the $registry property's array indexed by $storageId. */
        $this->registry[$storageId] = $this->generateRegistryData($storageId, $this->classify($data));
        /* If the data was registered in the registry property's array under the $storageId, update the
           stored registry. */
        if (isset($this->registry[$storageId]) === true) {
            /* Return true if stored registry was updated, false otherwise. */
            return $this->update(self::REGISTRY_ID, $this->registry);
        }
        /* Return false if data was not registered. */
        return false;
    }

    /**
     * Generates an array of registry data for data associated with the specified $storageId.
     * @param string $storageId The storage id of the data to generate registry data for.
     * @param string $classification The classification of the data to generate registry data for.
     * @param array $additionalData Array of additional registry data that should be included in the
     *                              generated registry data.
     * @return array Array of registry data for the specified $storageId.
     * Implementation Note: All implementations should return an array with at least the following structure:
     * array('storageId' => $storageId, 'classification' => $classification, 'additionalData' => $additionalData)
     * @todo Possible refactor to a defined method. This method is important because a registry data array has
     * @todo an expected structure. Also, the $additionalData property is currently unused.
     * @todo Refactor could be as simple as defining this method here, and having it call an abstract method
     * @todo called something like "getAdditionalRegistryData" that would allow implementations to define
     * @todo additional data that should be included in the registry data array.
     * @todo If the above refactor is done, this method should define the expected structure of the a
     * @todo registry data array, and merge the implementations registry data into the registry data array.
     *
     */
    abstract protected function generateRegistryData(string $storageId, string $classification, array $additionalData = array());

    /**
     * Determines the type, or fully qualified namespace and class, of a piece of packed data.
     * @param string $data The packed data to classify.
     * @return string The classification. If $data is an object the class name including the fully qualified
     *                namespace will be returned, otherwise one of the following will be returned:
     *                boolean, integer, double (same as float), string, array, resource, NULL, or unknown type.
     * @see gettype()
     * @see AregisteredCrud::unpack()
     * @see get_class()
     *
     */
    protected function classify(string $data): string
    {
        /* Determine the $data's type. */
        $classification = gettype($this->unpack($data));
        /* If $data is an object, return it's class name via the get_class() function, which will include the fully
           qualified namespace.*/
        if ($classification === 'object') {
            /* Return the object's class name via get_class(), this will include the fully qualified namespace. */
            return get_class($this->unpack($data));
        }
        /* Return the $data's type, will be one of the following: string, boolean, integer, double (same as float),
            or array. */
        return $classification;
    }

    /**
     * Updates stored data.
     * @param string $storageId The storage id of the data to update.
     * @param mixed $newData The new data.
     * @return bool True if data was updated, false otherwise.
     */
    final public function update(string $storageId, $newData): bool
    {
        /* Attempt to delete the original data associated with the specified storage id. */
        if ($this->delete($storageId) === true) {
            /* If the original data was successfully deleted, return true if the new
               data was created, false otherwise. */
            return $this->create($storageId, $newData);
        }
        /* If original data was not deleted, return false. */
        return false;
    }

    /**
     * Delete data from storage.
     * @param string $storageId The storage id of the data to delete.
     * @return bool True if data was deleted, false otherwise.
     */
    final public function delete(string $storageId): bool
    {
        /* Sync registry to make sure it is up to date with any changes that may have been made by another
           instance of the same implementation of this class. */
        $this->syncRegistry();
        /* Run a delete query on the data associated with the specified storage id. */
        if ($this->query($storageId, 'delete') !== false) {
            /* Never un-register the registry, the registry is managed by the registry methods. */
            if ($storageId !== self::REGISTRY_ID) {
                /* Return true if data was successfully deleted and un-registered, false otherwise. */
                return $this->unRegister($storageId);
            }
            /* When handling the registry, return true as long the delete query was successful. */
            return true;
        }
        /* Return false if delete query failed. */
        return false;
    }

    /**
     * Remove data from the registry.
     * @param string $storageId The storage id of the data to un-register from the registry.
     * @return bool True if data was un-registered and registry was updated, false otherwise.
     */
    private function unRegister(string $storageId): bool
    {
        /* Un-register data from the internal registry. */
        unset($this->registry[$storageId]);
        /* If data was un-registered from the internal registry, update the stored registry. */
        if (isset($this->registry[$storageId]) === false) {
            /* Return true if stored registry was updated, false otherwise. */
            return $this->update(self::REGISTRY_ID, $this->registry);
        }
        /* Return false if data was not registered. */
        return false;
    }

    /**
     * Returns the $registry property's array.
     * @return array The $registry property's array, which is an array of registered stored data.
     * @see AregisteredCrud::$registry
     * @see AregisteredCrud::register()
     * @see AregisteredCrud::unRegister()
     * @see AregisteredCrud::generateRegistryData()
     * @see AregisteredCrud::getRegistryData()
     */
    public function getRegistry(): array
    {
        return $this->registry;
    }

    /**
     * Get the registry data associated with a specified storage id.
     * @param string $storageId The storage id of the data to return registry data for.
     * @param string $name (optional) Name of a specific piece of registry data to return.
     *                                If not set, then all of the registry data associated
     *                                with the specified storage id will be returned.
     * @return mixed|bool The registry data for the data associated with the specified storage id, or
     *                    false on failure.
     * Implementation Note: False should be returned in the following circumstances:
     * 1. If there is no registry data for the specified $storageId.
     * 2. If there is no registry data associated with the specified
     *    registry data $name.
     */
    abstract public function getRegistryData(string $storageId, string $name = '*');

    /**
     * Syncs the $registry property with the stored registry.
     * @return bool True if the $registry property was synced with the stored registry, false otherwise.
     * Note: Whenever the stored registry is updated with the update() method the stored registry will be
     * temporarily deleted as a result of the logic of the update() method, which calls delete() prior
     * to calling create() to store the new registry data. This is the intended behavior, however, it may
     * lead one to believe there is a bug because the call to read() from this method will fail
     * when the update() method's call to create() calls this method to try and sync the registry
     * prior to storing the updated version of the registry data. This is not a bug, and the registry
     * will successfully sync on the next call to the create(), read(), or delete() method.
     *
     * Also note, there is no need to call this method from the update() method because update() calls delete()
     * and create(), an both of those methods call this method within there logic.
     */
    private function syncRegistry(): bool
    {
        /* Set the $syncing property to true to prevent infinite recursion loop when calling the read() method. */
        $this->syncing = true;
        /* If the stored registry exists and is an array, sync the $registry property to the stored registry. */
        if ($this->read(self::REGISTRY_ID) !== false && is_array($this->read(self::REGISTRY_ID)) === true) {
            /* Sync the $registry property with the stored registry. */
            $this->registry = $this->read(self::REGISTRY_ID);
            /* Set the $syncing property back to false. */
            $this->syncing = false;
            /* Return true if $registry property was synced with the stored registry and is not empty.
               i.e., $registry property is set and not an empty array, FALSE, NULL, 0, '0', '0.00', or
               an empty string '' */
            return !empty($this->registery);
        }
        /* Set the $syncing property back to false. */
        $this->syncing = false;
        /* Return false, the $registry was not synced because the stored registry does not exist. */
        return false;
    }
}
