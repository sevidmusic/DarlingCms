<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/5/17
 * Time: 10:15 AM
 */

namespace DarlingCms\classes\initializer;

/**
 * Class dcmsInitializer. Responsible for initializing any data needed by the Darling Cms. Initialized data
 * is stored in an array which can be retrieved by calling the getInitialized() method defined by the parent
 * Ainitializer abstract class.
 *
 * @package DarlingCms\classes\initializer
 */
class dcmsInitializer extends \DarlingCms\abstractions\initializer\Ainitializer
{
    /**
     * @var \DarlingCms\abstractions\crud\AregisteredCrud Registered Crud object used by the dcmsInitializer.
     */
    private $crud;

    /**
     * dcmsInitializer constructor. Assigns the provided registered crud implementation to the $crud property,
     * and also sets it as one of the initialized items under the index "crud".
     *
     * @param \DarlingCms\abstractions\crud\AregisteredCrud $crud Instance of an object that implements the \DarlingCms\abstractions\crud\AregisteredCrud abstract class.
     */
    public function __construct(\DarlingCms\abstractions\crud\AregisteredCrud $crud)
    {
        /* Assign the provided registered crud implementation to the $crud property. */
        $this->crud = $crud;
        /* Also, set the provided registered crud implementation as one of the initialized items under the index "crud". */
        $this->setInitialized($this->crud, 'crud'); // @todo: This may not be necessary, at the moment this is done so crud is available as one of the initialized items...
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
     * @return bool True if any data other then the crud was initialized, false otherwise.
     */
    public function initialize()
    {
        /* Initialize appStartupObjects. */
        $this->initAppStartupObjects();
        /* Initialize multiAppStartup object. */
        $this->initMultiAppStartupObject();
        /* Return true if there was any data initialized other then the crud, false otherwise. */
        return (count($this->initialized) > 1);
    }

    /**
     * Initialize the multiAppStartup object.
     * @return bool True if multiAppStartup object was initialized, false otherwise.
     */
    private function initMultiAppStartupObject()
    {
        /* If the array of initialized appStartupObjects was initialized, proceed. */
        if (isset($this->initialized['array']['appStartupObjects']) === true) {
            /* Get array of initialized appStartupObjects. */
            $appStartupObjects = $this->initialized['array']['appStartupObjects'];
            /* If the array of initialized appStartupObjects is not empty, proceed. */
            if (empty($appStartupObjects) === false) {
                /* Instantiate ReflectionClass object for the multiAppStartup instance. */
                $reflector = new \ReflectionClass('DarlingCms\classes\startup\multiAppStartup');
                /* Call the ReflectionClass's newInstanceArgs() method in order to pass the initialized
                 * singleAppStartup objects to the multiAppStartup class's constructor. This will create
                 * a new instance of a multiAppStartup object. This logic removes the need to loop through
                 * the array of singleAppStartupObjects.
                 */
                $multiAppStartupObject = $reflector->newInstanceArgs($appStartupObjects);
                /* Add multiAppStartup object to the $initialized property's array. */
                $this->setInitialized($multiAppStartupObject, 'multiAppStartup');
            }
        }
        /* Return true if multiAppStartup object was initialized, false otherwise. */
        return isset($this->initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup']);
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
            if ($this->crud->getRegistryData($storageId, 'classification') === 'DarlingCms\classes\component\app') {
                /* Load the app component. */
                if (($app = $this->crud->read($storageId)) !== false) {
                    /* Create a singleAppStartup instance for the app component and add it to the $appStartupObjects array. */
                    array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($app));
                }
            }
        }

        /* Determine if this is a fresh install of the Darling Cms based on whether
           or not any single app startup objects were instantiated for any app
           components registered with the crud. The assumptions is, since apps
           define the functionality of the Darling Cms, if there aren't any apps,
           registered with the crud, then this must be a fresh install.
         */
        if ((empty($appStartupObjects)) === true) {
            return $this->handleFreshInstall();
        }

        /* Add app startup objects array to the initialized array. */
        return $this->setInitialized($appStartupObjects, 'appStartupObjects');
    }

    /**
     * Performs required actions for a fresh Darling Cms install. Specifically, this method
     * looks for any apps that came installed with the Darling Cms, and registers their
     * respective app components with the crud. This method also enables any apps that
     * came installed with the Darling Cms by default so that they may be used "out of the box".
     * This approach will provide the user with the ability to use the installed apps without having
     * to perform any manual configuration of said apps. It also removes the need require any apps
     * be installed with the Darling Cms, making no assumptions about what apps should be installed,
     * which gives the user complete control over what functionality is available to their Darling Cms
     * installation "out of the box".
     *
     * @return bool True if all installed apps were successfully configured, false otherwise. A successful
     * configuration means that an app component was created for each app, and that app component was
     * successfully enabled and registered with the crud.
     */
    private function handleFreshInstall()
    {
        /* Determine if there are any apps installed. */
        $installedApps = $this->determineInstalledApps();
        switch (empty($installedApps)) {
            case true:
                /* Show user message indicating that no apps came installed with this installation of the Darling Cms. */
                echo '<h1 style="font-size: 2.3em;">Welcome to the Darling Cms</h1>
                      <p style="font-size: 2em;">There weren\'t any apps installed with this installation of the Darling Cms.
                       Install at least one app in the "apps" directory, and then reload this page.
                       You can find apps for the Darling Cms online @ <a href="https://github.com/sevidmusic/dcmsApps">
                       https://github.com/sevidmusic/dcmsApps</a>
                       Or, if you know what your doing, develop your own apps, install them in the 
                       apps directory, and reload this page.</p>';
                /* Return false if there were no installed apps to configure. */
                return false;
            default:
                /* Initialize an array to track the success or failure of each app's configuration. */
                $status = array();
                foreach ($installedApps as $appName) {
                    /* Create an app component for the app. */
                    $app = new \DarlingCms\classes\component\app($appName);
                    /* Enable the app. Track success or failure via the $status array. */
                    $status[] = $app->enableApp();
                    /* Register the new app component with the crud. Track success or failure via the $status array. */
                    $status[] = $this->crud->create($appName, $app);
                }
                if ((in_array(false, $status, true) === false)) {
                    /* Show simple welcome message. */
                    echo '<h1 style="font-size: 2.3em;">Welcome to your new installation of the Darling Cms.</h1>
                      <p style="font-size: 2em;">The following apps have been enabled for your new installation of the Darling Cms:<br><br>
                      ' . implode('<br>', $installedApps) . '
                      </p><p style="font-size: 2em;">Reload the page to start using your new installation</p>';/* Check $status array and return true if all installed apps were configured successfully, false otherwise. */;
                    /* Return true if all installed apps were successfully configured. */
                    return true;
                }
                echo '<h1 style="font-size: 2.3em;">Welcome to your new installation of the Darling Cms.</h1>
                      <p style="font-size: 2em;">Not all of the installed apps could be enabled for you, you can still proceed to your new installation by reloading this page.</p>';
                /* Return false if any installed apps were not successfully configured. Note: Some apps my still have been successfully configured. */
                return false;
        }
    }

    /**
     * Remove dot file refs from array being processed by array_filter().
     * NOTE: This method is called by array_filter() in the determineInstalledApps() method.
     * @param $value string The file name.
     * @return mixed The file name or false if the file name matched a dot file.
     */
    private function stripDots($value)
    {
        /* Create an array of values to ignore. */
        $ignore = array('.', '..', '.DS_Store');
        /* Unset any $value if it matches any of the values in the $ignore array. */
        if (in_array($value, $ignore, true)) {
            unset($value);
        }
        /* If value does not match any of the values in the $ignore array, return it.  */
        if (isset($value)) {
            return $value;
        }
        /* Return false if $value was unset. */
        return false;
    }

    /**
     * Determines what apps are installed.
     * @return array Array of installed app names.
     */
    private function determineInstalledApps()
    {
        return array_unique(array_filter(scandir(__DIR__ . '/../../../apps'), array($this, 'stripDots')));
    }
}
