<?php


namespace DarlingCms\interfaces\startup;

/**
 * Interface IMultiAppStartup. Defines the basic contract of an object
 * that is responsible for handling the startup, shutdown, and restart
 * logic of multiple Darling Cms apps.
 *
 * @package DarlingCms\interfaces\startup
 */
interface IMultiAppStartup extends IAppStartup
{
    /**
     * Returns the specified app's output, or an empty string if the specified
     * app does not have any output, or if the attempt to get the app's output
     * failed.
     *
     * WARNING: This method will return an empty string in the case of failure
     *          as well as if the app does not have any output, so it is not
     *          reliable to test if this method's return value is empty to
     *          determine whether or not this method was successful.
     *
     * @param string $appName The name of the app whose output should be returned.
     *
     * @return string The specified app's output, or an empty string if the
     *                specified app does not have any output, or if the attempt
     *                to get the app's output failed.
     */
    public function getSpecifiedAppOutput(string $appName): string;

    /**
     * Returns the collective output of all the apps that were
     * successfully started up by this IMultiAppStartup
     * implementation instance.
     *
     * @return string The collective output of all the apps that were
     *                successfully started up by this IMultiAppStartup
     *                implementation instance.
     */
    public function getAppOutput(): string;
}
