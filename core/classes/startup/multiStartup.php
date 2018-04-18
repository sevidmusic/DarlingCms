<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/26/17
 * Time: 5:10 PM
 */

namespace DarlingCms\classes\startup;

/**
 * Class darlingCmsStartup. General startup object that can be used to start up, restart, and shut down
 * multiple objects that implement the \DarlingCms\interfaces\startup\Istartup interface.
 *
 * @package DarlingCms\classes\startup
 * @see \DarlingCms\interfaces\startup\Istartup
 * @see \DarlingCms\abstractions\startup\Astartup
 */
class multiStartup extends \DarlingCms\abstractions\startup\Astartup
{
    /**
     * @var array Array of objects that implement the \DarlingCms\interfaces\startup\Istartup interface.
     */
    protected $startupObjects = array();

    /**
     * darlingCmsStartup constructor. Adds the specified startup objects to the $startupObjects property's array
     * via the multiStartup::setStartupObject() method.
     * Note: The constructor will throw an Exception and log an error if any of the specified $startupObjects could
     * not be set by the multiStartup::setStartupObject() method.
     * @param \DarlingCms\interfaces\startup\Istartup[] ...$startupObjects Instances of objects that implement the
     *                                                                     \DarlingCms\interfaces\startup\Istartup
     *                                                                     interface.
     * @see multiStartup::setStartupObject()
     * @see \Exception
     */
    public function __construct(\DarlingCms\interfaces\startup\Istartup ...$startupObjects)
    {
        /* Add each startup object to the $startupObjects property's array. */
        foreach ($startupObjects as $startUpObject) {
            try {
                /* If setStartupObject() returns false, throw an error. */
                if ($this->setStartupObject($startUpObject) === false) {
                    throw new \Exception('Darling Cms Startup Error:');
                }
            } catch (\Exception $e) {
                /* If any of the calls to setStartupObject() returned false, log an error. */
                error_log('Failed to add startup object in ' . $e->getFile() . ' on line ' . $e->getLine() . PHP_EOL . $e->getTraceAsString());
            }
        }
    }

    /**
     * Adds a startup object to the $startupObjects property's array.
     * @param \DarlingCms\interfaces\startup\Istartup $startupObject The startup object to add.
     * @return bool True if startup object was added to the $startupObjects property's array, false otherwise.
     */
    public function setStartupObject(\DarlingCms\interfaces\startup\Istartup $startupObject): bool
    {
        /* Count initial number of startup objects. */
        $initialCount = count($this->startupObjects);
        /* Push startup object into the startup objects array. */
        array_push($this->startupObjects, $startupObject);
        /* Get final count of startup objects. */
        $finalCount = count($this->startupObjects);
        /* Return true if startup object was added to the startup objects array, false otherwise. */
        return ($initialCount < $finalCount);
    }

    /**
     * Calls the shutdown() method of each startup object assigned to the $startupObjects property's array.
     * @return bool True if each startup object shutdown successfully, false otherwise.
     * @see \DarlingCms\interfaces\startup\Istartup::shutdown()
     */
    protected function stop(): bool
    {
        /* Initialize status array. Tracks success or failure of each call to shutdown(). */
        $status = array();
        /* Shutdown each startup object. */
        foreach ($this->startupObjects as $startupObject) {
            /* Call shutdown() and store result in $status array. */
            array_push($status, $startupObject->shutdown());
        }
        /* Return true if all calls to shutdown() returned true, false otherwise. */
        return (in_array(false, $status) === false);
    }

    /**
     * Calls the startup() method of each startup object.
     * @return bool True if each startup object started up successfully, false otherwise.
     * @see \DarlingCms\interfaces\startup\Istartup::startup()
     */
    protected function run(): bool
    {
        /* Initialize status array. Tracks success or failure of each call to startup(). */
        $status = array();
        foreach ($this->startupObjects as $startupObject) {
            /* Call startup() and store result in $status array. */
            array_push($status, $startupObject->startup());
        }
        /* Return true if all calls to setStartupObject() returned true, false otherwise. */
        return (in_array(false, $status) === false);
    }

}
