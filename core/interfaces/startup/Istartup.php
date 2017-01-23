<?php namespace DarlingCms\interfaces\startup;

/**
 * Interface Istartup. Defines the interface for an object that
 * provides startup, shutdown, and restart methods.
 *
 * @package DarlingCms\interfaces\startup
 */
interface Istartup
{
    /**
     * Initiate the startup process. This method should return a boolean,
     * true if startup was successful, false otherwise.
     *
     * @return bool True if startup was successful, false otherwise.
     */
    public function startup();

    /**
     * Initiate the shutdown process. This method should return a boolean,
     * true if shutdown was successful, false otherwise.
     *
     * @return bool True if shutdown was successful, false otherwise.
     */
    public function shutdown();

    /**
     * Restart the startup process. This method should return a boolean,
     * true if restart was successful, false otherwise.
     *
     * @return bool True if restart was successful, false otherwise.
     */
    public function restart();

}