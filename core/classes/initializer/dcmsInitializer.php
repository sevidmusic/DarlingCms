<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/5/17
 * Time: 10:15 AM
 */

namespace DarlingCms\classes\initializer;


class dcmsInitializer extends \DarlingCms\abstractions\initializer\Ainitializer
{
    /**
     * @var array
     */
    private $crud;

    /**
     * initializer constructor.
     */
    public function __construct(\DarlingCms\abstractions\crud\AregisteredCrud $crud)
    {
        $this->crud = $crud;
        $this->setInitialized($this->crud, 'crud');
    }

    /**
     * Add an item to the initialized array.
     * @param mixed $item The initialized item.
     * @param string $index (optional) An optional index to assign to the item.
     * @return bool True if item was added to the initialized array false otherwise.
     */
    private function setInitialized($item, string $index = '')
    {
        if ($index !== '') {
            return $this->handleAssociativeIndex($index, $item);
        }
        return $this->handleNumericIndex($item);
    }

    /**
     * Handle associative indexing of an item in the initialized array.
     * @param string $index The index to use.
     * @param mixed $item The item being set in the initialized array.
     * @return bool True if item was set in the initialized array under the specified index, false otherwise.
     */
    private function handleAssociativeIndex(string $index, $item)
    {
        /* Handle associative index. */
        switch (gettype($item)) {
            case 'object':
                $classification = trim(get_class($item));
                break;
            default:
                $classification = trim(gettype($item));
                break;
        }
        $this->initialized[$classification][$index] = $item;
        return isset($this->initialized[$classification][$index]);
    }

    /**
     * Handle numeric indexing of an item in the initialized array.
     * Note: This numeric index will be generated internally.
     * @param mixed $item The item being set in the initialized array.
     * @return bool True if item was set in the initialized array, false otherwise.
     */
    private function handleNumericIndex($item)
    {
        /* Handle numeric index. */
        switch (gettype($item)) {
            case 'object':
                $classification = trim(get_class($item));
                break;
            default:
                $classification = trim(gettype($item));
                break;
        }
        $this->initialized[$classification][] = $item;
        return in_array($item, $this->initialized[$classification], true);
    }

    /**
     * @return bool True if initialization was successful, false otherwise.
     */
    public function initialize()
    {
        /* Initialize appStartupObjects. */
        $this->initAppStartupObjects();

        return true;
    }

    /**
     * Initialize singleAppStartup object instances for any apps
     * registered with the crud.
     */
    private function initAppStartupObjects()
    {
        /* Initialize appStartupObjects array. */
        $appStartupObjects = array();

        /* Search registry for any data classified as an app component. */
        foreach (array_keys($this->crud->getRegistry()) as $storageId) {
            /* Check if the data's classification indicates an app component. */
            switch ($this->crud->getRegistryData($storageId, 'classification')) {
                case 'DarlingCms\classes\component\app':
                    /* Load the app component. */
                    if (($app = $this->crud->read($storageId)) !== false) {
                        /* Create a singleAppStartup instance for the app component and add it to the $appStartupObjects array. */
                        array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($app));
                    }
                    break;
                case 'DarlingCms\classes\component\appPackage':
                    // unpack apps from app package and create startup objects for them
                    $package = $this->crud->read($storageId);

                    foreach ($package->getComponentAttributes()['customAttributes']['apps'] as $app) {
                        array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($this->crud->read($app)));
                    }
                    break;
            }
        }

        /* Determine if this is a fresh install of the Darling Cms based on whether
           or not any single app startup objects were instantiated for any app
           components registered with the crud. */
        if ((empty($appStartupObjects)) === true) {
            return $this->handleFreshInstall();
        }

        /* Add app startup objects array to the initialized array. */
        return $this->setInitialized($appStartupObjects, 'appStartupObjects');
    }

    /**
     * Performs required actions for a fresh Darling Cms install. Specifically, instantiates
     * a new app component and singleAppStartup startup object for the "appManager" app.
     *
     * Note: The appManager app is responsible for managing installed Darling Cms apps, and therefore,
     * is assigned the responsibility of enabling and configuring core apps for a fresh install.
     *
     * WARNING: The appManager app comes pre-installed with the Darling Cms. If it was removed, configuration
     * of a new installation will have to be done manually.
     *
     */
    private function handleFreshInstall()
    {
        /* Temporarily enable appManager app for fresh install. */
        $appManager = new  \DarlingCms\classes\component\app('appManager');
        $appManager->enableApp();
        $appManager->registerTheme('appManager');
        /* Initialize singleAppStartup() object for appManager app, wrap in an array and overwrite the initialized array's
           appStartupObjects array. */
        return $this->setInitialized(array(new \DarlingCms\classes\startup\singleAppStartup($appManager)), 'appStartupObjects');
    }
}
