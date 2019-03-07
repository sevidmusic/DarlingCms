<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-03-07
 * Time: 10:52
 */

namespace DarlingCms\interfaces\crud;


use DarlingCms\interfaces\config\ISiteConfiguration;

/**
 * Interface ISiteConfigurationCrud. Defines the basic contract of an object that can be used
 * to create, read, update, and delete ISiteConfiguration implementation instances.
 * @package DarlingCms\interfaces\crud
 */
interface ISiteConfigurationCrud
{
    /**
     * Create a new site configuration.
     * @param ISiteConfiguration $siteConfiguration The ISiteConfiguration implementation instance
     *                                              that represents the site configuration.
     * @return bool
     */
    public function create(ISiteConfiguration $siteConfiguration): bool;

    /**
     * Read a specified site configuration.
     * @param string $configurationName The name of the configuration to read.
     * @return ISiteConfiguration The specified ISiteConfiguration implementation instance.
     */
    public function read(string $configurationName): ISiteConfiguration;

    /**
     * Update a specified site configuration.
     * @param string $configurationName The name of the site configuration to update.
     * @param ISiteConfiguration $newSiteConfiguration The ISiteConfiguration implementation
     *                                                 instance that represents the updated
     *                                                 site configuration.
     * @return bool True if update was successful, false otherwise.
     */
    public function update(string $configurationName, ISiteConfiguration $newSiteConfiguration): bool;

    /**
     * Delete a specified site configuration.
     * @param string $configurationName The name of the site configuration to delete.
     * @return bool True if the specified site configuration was deleted, false otherwise.
     */
    public function delete(string $configurationName): bool;
}
