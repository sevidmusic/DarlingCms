<?php

namespace DarlingCms\classes\config;

use DarlingCms\interfaces\accessControl\IAppConfig;

/**
 * Class EmptyAppConfig. Defines an implementation of the IAppConfig interface
 * that can be used to create empty IAppConfig implementation instances.
 *
 * This class can be useful, for instance, if a default IAppConfig implementation
 * instance is needed, or as a dummy IAppConfig implementation instance that can
 * be used during development.
 *
 * @package DarlingCms\classes\config
 */
class EmptyAppConfig implements IAppConfig
{
    /**
     * Validates access.
     *
     * Note: This implementation will always return false.
     *
     * @return bool True if access is valid, false otherwise.
     */
    public function validateAccess(): bool
    {
        return false;
    }

    /**
     * Gets the app's name.
     *
     * Note: This implementation will always return an empty string.
     *
     * @return string The app's name.
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * Gets an array of the names of the themes assigned to the app.
     *
     * Note: This implementation will always return an empty array.
     *
     * @return array Array of the names of the themes assigned to the app.
     */
    public function getThemeNames(): array
    {
        return array();
    }

    /**
     * Gets an array of the names of the javascript libraries assigned to the app.
     *
     * Note: This implementation will always return an empty array.
     *
     * @return array Array of the names of the javascript libraries assigned to the app.
     */
    public function getJsLibraryNames(): array
    {
        return array();
    }
}
