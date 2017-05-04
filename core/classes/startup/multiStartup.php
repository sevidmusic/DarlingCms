<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/26/17
 * Time: 5:10 PM
 */

namespace DarlingCms\classes\startup;

/**
 * Class darlingCmsStartup. General startup object that starts up, restarts, and shuts down objects
 * that implement the \DarlingCms\interfaces\startup\Istartup interface.
 *
 * @package DarlingCms\classes\startup
 */
class multiStartup extends \DarlingCms\abstractions\startup\Astartup
{
    /**
     * @var array Array of objects that implement the \DarlingCms\interfaces\startup\Istartup interface.
     */
    protected $startupObjects;

    /**
     * darlingCmsStartup constructor. Adds the startup objects passed the constructor to the startup objects array.
     * @param \DarlingCms\interfaces\startup\Istartup[] ...$startupObjects Instances of objects that implement the
     *                                                                     \DarlingCms\interfaces\startup\Istartup
     *                                                                     interface.
     */
    public function __construct(\DarlingCms\interfaces\startup\Istartup ...$startupObjects)
    {
        /* Initialize status array. Tracks success or failure of each call to setStartupObject(). */
        $status = array();
        /* Add each startup object to the startup objects array. */
        foreach ($startupObjects as $startUpObject) {
            /* Call setStartupObject() and store result in $status array. */
            array_push($status, $this->setStartupObject($startUpObject));
        }
        /* Return true if all calls to setStartupObject() returned true, false otherwise. */
        return (in_array(false, $status) === false);
    }

    /**
     * Adds a startup object to the internal $startupObjects array.
     *
     * @param \DarlingCms\interfaces\startup\Istartup $startupObject The startup object to add.
     *
     * @return bool True if startup object was added to the startup objects array, false otherwise.
     */
    public function setStartupObject(\DarlingCms\interfaces\startup\Istartup $startupObject)
    {
        /* Make sure startup objects array has been initialized. */
        if (is_array($this->startupObjects) === false) {
            /* Initialize startup objects array. */
            $this->startupObjects = array();
        }
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
     * Class the shutdown() method of each startup object.
     *
     * @return bool True if each startup object shutdown successfully, false otherwise.
     */
    protected function stop()
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
     * Class the startup() method of each startup object.
     *
     * @return bool True if each startup object started up successfully, false otherwise.
     */
    protected function run()
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
