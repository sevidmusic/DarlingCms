<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/5/17
 * Time: 10:15 AM
 */

namespace DarlingCms\classes\initializer;

use DarlingCms\abstractions\crud\AregisteredCrud;
use DarlingCms\abstractions\initializer\Ainitializer;
use DarlingCms\classes\component\html\html;
use DarlingCms\classes\installer\appInstaller;

/**
 * Class dcmsInitializer. Responsible for initializing any data needed by the Darling Cms. Initialized data
 * is stored in an array which can be retrieved by calling the getInitialized() method defined by the parent
 * Ainitializer abstract class.
 * @package DarlingCms\classes\initializer
 * @see \DarlingCms\abstractions\initializer\Ainitializer::getInitialized()
 */
class dcmsInitializer extends Ainitializer
{
    /**
     * @var \DarlingCms\abstractions\crud\AregisteredCrud Instance of an AregisteredCrud implementation
     *                                                    used by the dcmsInitializer to interact with
     *                                                    storage.
     */
    private $crud;

    /**
     * dcmsInitializer constructor. Assigns the specified AregisteredCrud implementation instance to
     * the $crud property.
     *
     * @param \DarlingCms\abstractions\crud\AregisteredCrud $crud Instance of an object that implements the
     *                                                            \DarlingCms\abstractions\crud\AregisteredCrud
     *                                                            abstract class.
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     */
    public function __construct(AregisteredCrud $crud)
    {
        /* Assign the provided AregisteredCrud implementation instance to the $crud property. */
        $this->crud = $crud;
    }

    /**
     * Returns the AregisteredCrud implementation instance assigned to the dcmsInitializer's $crud property.
     * @return \DarlingCms\abstractions\crud\AregisteredCrud The AregisteredCrud implementation assigned
     *                                                       to the dcmsInitializer's $crud property.
     * @see AregisteredCrud
     */
    public function getCrud(): AregisteredCrud
    {
        return $this->crud;
    }

    /**
     * Add an item to the initialized array.
     * @param mixed $item The item.
     * @param string $index (optional) An optional index to assign to the item.
     * Note: Data in the $initialized property's array is always indexed first by classification, then by the
     * appropriate index. Data is classified based on type, or fully qualified class name for objects.
     * i.e., $initialize[type][INDEX] or $initialized[\Namespace\ClassName][INDEX]
     * @return bool True if item was added to the initialized array, false otherwise.
     * @see dcmsInitializer::handleAssociativeIndex()
     * @see dcmsInitializer::handleNumericIndex()
     */
    private function setInitialized($item, string $index = ''): bool
    {
        if ($index !== '') {
            return $this->handleAssociativeIndex($index, $item);
        }
        return $this->handleNumericIndex($item);
    }

    /**
     * Handle associative indexing of an item in the initialized array.
     * Note: Data added to the $initialized property's array by this method is always indexed first by classification,
     * then by the specified $index. Data is classified based on type, or fully qualified class name for objects.
     * i.e., $initialize[type][$index] or $initialized[\Namespace\ClassName][$index]
     * @param string $index The index to assign to the data.
     * @param mixed $item The item being set in the initialized array.
     * @return bool True if item was set in the initialized array under the specified index, false otherwise.
     */
    private function handleAssociativeIndex(string $index, $item): bool
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
     * Note: Data added to the $initialized property's array by this method is always indexed first by classification,
     * then by the numeric index. Data is classified based on type, or fully qualified class name for objects.
     * i.e., $initialize[type][NUMERIC_INDEX] or $initialized[\Namespace\ClassName][NUMERIC_INDEX]
     * Note: This numeric index will be generated internally.
     * @param mixed $item The item being set in the initialized array.
     * @return bool True if item was set in the initialized array, false otherwise.
     */
    private function handleNumericIndex($item): bool
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
     * Initializes the appropriate data. Currently this method initializes the
     * singleAppStartup objects, and the multiAppStartup object.
     * @return bool True if data was initialized, false otherwise.
     * @see dcmsInitializer::initAppStartupObjects()
     * @see dcmsInitializer::initMultiAppStartupObject()
     * @see \DarlingCms\classes\startup\singleAppStartup
     * @see \DarlingCms\classes\startup\multiAppStartup
     */
    public function initialize(): bool
    {
        /* Initialize an array to track the success of each method this method calls. */
        $status = array();
        /* Initialize appStartupObjects. */
        array_push($status, $this->initAppStartupObjects());
        /* Initialize multiAppStartup object. */
        array_push($status, $this->initMultiAppStartupObject());
        /* Return true if there was any data initialized, false otherwise. */
        return in_array(false, $status, true);
    }

    /**
     * Initialize the multiAppStartup object.
     *
     * Note: This method uses PHP's ReflectionClass to instantiate a multiAppStartup object instance.
     * .
     * Note: If there are no singleAppStart objects initialized, this method will log an error indicating
     * the Darling Cms failed to start, and return false.
     *
     * Note: If the multiAppStartup object could not be initialized, this method will log an error indicating
     * the Darling Cms failed to start, and return false.
     *
     * @return bool True if multiAppStartup object was instantiated and assigned to the initialized property's array,
     *              false otherwise.
     * @see \ReflectionClass
     * @see \ReflectionException
     * @see \ReflectionClass::newInstanceArgs()
     * @see dcmsInitializer::setInitialized()
     */
    private function initMultiAppStartupObject(): bool
    {
        /* If the array of initialized singleAppStartup objects is not set, or the array of initialized
           singleAppStartup objects is empty, log an error and return false. */
        if (isset($this->initialized['array']['appStartupObjects']) === false || empty($this->initialized['array']['appStartupObjects']) === true) {
            error_log('Darling Cms Startup Error: There are no apps installed. Error occurred in ' . __FILE__ . ' on line ' . (__LINE__ - 1));
            return false;
        }
        /* Instantiate ReflectionClass object for the multiAppStartup instance. */
        try {
            $reflector = new \ReflectionClass('DarlingCms\classes\startup\multiAppStartup');
        } catch (\ReflectionException $e) {
            /* Failed to instantiate a ReflectionClass object for the multiAppStartup class, log error and
               return false. */
            error_log('Darling Cms Startup Error: The Darling Cms failed to start!' . PHP_EOL . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile() . PHP_EOL . 'Stack Trace:' . PHP_EOL . $e->getTraceAsString() . PHP_EOL . 'Exception Code: ' . $e->getCode() . PHP_EOL);
            return false;
        }
        /* Call the ReflectionClass's newInstanceArgs() method in order to pass the initialized
         * singleAppStartup objects to the multiAppStartup class's constructor. This will create
         * a new instance of a multiAppStartup object. This logic removes the need to loop through
         * the array of singleAppStartupObjects.
         */
        $multiAppStartupObject = $reflector->newInstanceArgs($this->initialized['array']['appStartupObjects']);
        /* Add multiAppStartup object to the $initialized property's array. */
        return $this->setInitialized($multiAppStartupObject, 'multiAppStartup');
    }

    /**
     * Initialize singleAppStartup object instances for any apps
     * registered with the crud.
     * @return bool True if array of singleAppStartup objects was added to the $initialized property's array,
     *              false otherwise. If this is a fresh install of the Darling Cms, this method will return
     *              true if the dcmsInitializer::freshInstall() method returned true, false otherwise.
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     * @see \DarlingCms\abstractions\crud\AregisteredCrud::getRegistry()
     * @see \DarlingCms\abstractions\crud\AregisteredCrud::read()
     * @see \DarlingCms\classes\startup\singleAppStartup
     * @see dcmsInitializer::handleFreshInstall()
     * @see dcmsInitializer::setInitialized()
     */
    private function initAppStartupObjects(): bool
    {
        /* Initialize an array for the singleAppStartup objects. */
        $appStartupObjects = array();
        /* Search registry for any data classified as an app component. */
        foreach (array_keys($this->crud->getRegistry()) as $storageId) {
            /* Load the app component. */
            if (($app = $this->crud->read($storageId, 'DarlingCms\classes\component\app')) !== false) {
                /* Create a singleAppStartup instance for the app component and add it to the $appStartupObjects array. */
                array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($app));
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
     * @see \DarlingCms\classes\component\html\htmlHead
     * @see \DarlingCms\classes\component\html\htmlHead::prependHtml()
     * @see \DarlingCms\classes\component\html\html
     * @see \DarlingCms\classes\userInterface\userInterface
     * @see \DarlingCms\classes\userInterface\userInterface::addOpeningHtml()
     * @see \DarlingCms\classes\component\html\htmlContainer
     * @see dcmsInitializer::determineInstalledApps()
     * @see \DarlingCms\classes\component\html\htmlContainer::appendHtml()
     * @see \DarlingCms\classes\userInterface\userInterface::getUserInterface()
     * @see \DarlingCms\classes\installer\appInstaller
     * @see \DarlingCms\classes\installer\appInstaller::enableApp()
     * @see \DarlingCms\classes\installer\appInstaller::install()
     */
    private function handleFreshInstall(): bool
    {
        /* Instantiate the htmlHead object used to generate the pages html header.*/
        $htmlHead = new \DarlingCms\classes\component\html\htmlHead($this->getCrud());
        /* Create a meta tag to set the viewport and prepend it to the htmlHead's html container. */
        $htmlHead->prependHtml(new \DarlingCms\classes\component\html\html('meta', '', array('name="viewport"', 'content="width=device-width, initial-scale=1.0"')));
        /* Create the user interface. */
        $userInterface = new \DarlingCms\classes\userInterface\userInterface();
        /* Set the user interface's container type to "html". */
        $userInterface->containerType = 'html';
        /* Add the htmlHead's html container to the user interface's opening html. */
        $userInterface->addOpeningHtml($htmlHead);
        /* Create the body html tag, and assign the app output as it's content. */
        $body = new \DarlingCms\classes\component\html\htmlContainer('body', array('class="dcmsFreshInstall"'));
        /* Create the "Welcome" header. */
        $welcomeHeader = new html('h1', 'Welcome to your new installation of the Darling Cms.');
        /* Determine if there are any apps installed. */
        $installedApps = $this->determineInstalledApps();
        switch (empty($installedApps)) {
            case true:
                /* Show user message indicating that no apps came installed with this installation of the Darling Cms. */
                $welcomeMessage = new html('p', 'There weren\'t any apps installed with this installation of the Darling Cms. Install at least one app in the "apps" directory, and then reload this page. You can find apps for the Darling Cms online @ <a href="https://github.com/sevidmusic/dcmsApps">https://github.com/sevidmusic/dcmsApps</a>Or, if you know what your doing, develop your own apps, install them in the apps directory, and reload this page.');
                $body->appendHtml($welcomeHeader, $welcomeMessage);
                echo '<!DOCTYPE html>' . $userInterface->getUserInterface($body);
                /* Return false if there were no installed apps to configure. */
                return false;
            default:
                /* Initialize an array to track the success or failure of each app's configuration. */
                $status = array();
                foreach ($installedApps as $appName) {
                    $appInstaller = str_replace('core/classes/initializer', '', __DIR__) . 'apps/' . $appName . '/installer.php';
                    switch (file_exists($appInstaller)) {
                        case true:
                            $status[] = boolval(require $appInstaller);
                            break;
                        default:
                            /* Instantiate an app installer for the app. */
                            $appInstaller = new appInstaller($appName, $this->crud);
                            /* Enable the app. */
                            $status[] = $appInstaller->enableApp();
                            /* Install the app. */
                            $status[] = $appInstaller->install();
                            break;
                    }
                }
                if ((in_array(false, $status, true) === false)) {
                    $welcomeMessage = new html('p', 'The following apps have been enabled for your new installation of the Darling Cms:<br><br>' . implode('<br>', $installedApps) . '<br><br>Reload the page to start using your new installation');
                    $body->appendHtml($welcomeHeader, $welcomeMessage);
                    echo '<!DOCTYPE html>' . $userInterface->getUserInterface($body);
                    return true;
                }
                $welcomeMessage = new html('p', 'Not all of the installed apps could be enabled for you, you can still proceed to your new installation by reloading this page.');
                $body->appendHtml($welcomeHeader, $welcomeMessage);
                echo '<!DOCTYPE html>' . $userInterface->getUserInterface($body);
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
    private function determineInstalledApps(): array
    {
        return array_unique(array_filter(scandir(__DIR__ . '/../../../apps'), array($this, 'stripDots')));
    }
}
