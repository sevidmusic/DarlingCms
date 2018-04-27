<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/26/18
 * Time: 11:26 PM
 */

namespace DarlingCms\interfaces\startup;

/**
 * Interface IStartup. Defines the basic contract of an object that handles startup, shutdown, and restart logic.
 * @package DarlingCms\interfaces\startup
 * @see IStartup::startup()
 * @see IStartup::shutdown()
 * @see IStartup::restart()
 */
interface IStartup
{
    /**
     * Handles startup logic.
     * @return bool True if startup was successful, false otherwise.
     */
    public function startup(): bool;

    /**
     * Handles shutdown logic.
     * @return bool True if shutdown was successful, false otherwise.
     */
    public function shutdown(): bool;

    /**
     * Handles restart logic.
     * @return bool True if restart was successful, false otherwise.
     */
    public function restart(): bool;
}
