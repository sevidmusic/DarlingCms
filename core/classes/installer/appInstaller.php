<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/29/18
 * Time: 7:18 PM
 */

namespace DarlingCms\classes\installer;


use DarlingCms\abstractions\crud\AregisteredCrud;
use DarlingCms\classes\accessControl\accessController;
use DarlingCms\classes\component\app;
use DarlingCms\classes\component\html\htmlHead;
use DarlingCms\interfaces\installer\Iinstaller;

/**
 * Class appInstaller. Manages installation and un-installation of a specified app.
 * @package DarlingCms\classes\installer
 * @see AregisteredCrud
 * @see app
 */
class appInstaller implements Iinstaller
{
    /**
     * @var string The name of the app this installer manages.
     */
    private $appName;

    /**
     * @var AregisteredCrud The AregisteredCrud implementation assigned to this installer.
     * @see AregisteredCrud
     */
    private $crud;

    /**
     * @var app The app component instance for the app managed by this installer.
     * @see app
     */
    private $appComponent;

    /**
     * @var htmlHead The htmlHead object used by this installer.
     */
    private $htmlHead;

    /**
     * appInstaller constructor. Assigns the specified $appName to the $appName property. Assigns the specified
     * AregisteredCrud implementation to the $curd property. Instantiates an app component for the app managed
     * by this installer.
     * @param string $appName The name of the app this installer manages.
     * @param AregisteredCrud $crud The AregisteredCrud implementation to assign to this installer.
     * @param array $customAttributes Array of custom attributes to pass to the app component instantiated for
     *                                the app managed by this installer.
     * @see AregisteredCrud
     * @see app
     */
    public function __construct(string $appName, AregisteredCrud $crud, array $customAttributes = array())
    {
        /* Assign the specified $appName to the $appName property. */
        $this->appName = $appName;
        /* Assign the specified AregisteredCrud implementation to the $crud property. */
        $this->crud = $crud;
        /* Instantiate an app component for the app this installer manages, and assign it to the appComponent property. */
        $this->appComponent = new app($appName, $customAttributes);
        /* Instantiate an htmlHead for this installer. */
        $this->htmlHead = new htmlHead($this->crud);
    }

    /**
     * Perform installation.
     * @return bool Return true on success, false on failure.
     * @see app
     * @see AregisteredCrud
     */
    public function install(): bool
    {
        $status = array();
        /* Enable the app. */
        array_push($status, $this->appComponent->enableApp());
        /* Enable any themes registered with the app. */
        foreach ($this->getThemes() as $theme) {
            array_push($status, $this->htmlHead->enableTheme($theme));
        }
        /* Store the app component. */
        array_push($status, $this->crud->create($this->appName, $this->appComponent));
        /* Return true on success, false on failure. */
        return !in_array(false, $status, true);
    }

    /**
     * Perform un-installation.
     * @return bool Return true on success, false on failure.
     */
    public function unInstall(): bool
    {
        $status = array();
        /* Disable the app. */
        array_push($status, $this->appComponent->enableApp());
        /* Update the stored app component. */
        array_push($status, $this->crud->create($this->appName, $this->appComponent));
        /* Return true on success, false on failure. */
        return !in_array(false, $status, true);
    }

    /**
     * Register a dependency for the app managed by this installer.
     * @param string $dependency The name of the app to register as a dependency.
     * Note: An app cannot register itself as a dependency, attempting to register
     * the app managed by this installer as a dependency will cause this method
     * to return false.
     * @return bool True if dependency was registered, false otherwise.
     */
    public function registerDependency(string $dependency): bool
    {
        return $this->appComponent->registerDependency($dependency);
    }

    /**
     * Register a theme for the app managed by this installer.
     * @param string $theme The name of the theme to register.
     * @return bool True if theme was registered, false otherwise.
     */
    public function registerTheme(string $theme): bool
    {
        return $this->appComponent->registerTheme($theme);
    }

    /**
     * Set a custom attribute for the app managed by this installer.
     * @param string $customAttributeKey The key to assign to the custom attribute.
     * @param mixed $customAttributeValue The value of the custom attribute.
     * @return bool True if custom attribute was set, false otherwise.
     */
    public function setCustomAttribute(string $customAttributeKey, $customAttributeValue): bool
    {
        return $this->appComponent->setCustomAttribute($customAttributeKey, $customAttributeValue);
    }

    /**
     * Determines if the app managed by this installer is assigned an accessController.
     * @return bool True if the app managed by this installer is assigned an accesssController, false otherwise.
     */
    public function hasAccessController(): bool
    {
        return $this->appComponent->hasAccessController();
    }

    /**
     * Returns the accessController assigned to the app managed by this installer, or false
     * if an accessController is not assigned to the app managed by this installer.
     * @return bool|\DarlingCms\classes\accessControl\accessController The accessController assigned to the
     *                                                                 app managed by this installer, or false
     *                                                                 if an accessController is not assigned to
     *                                                                 the app managed by this installer.
     */
    public function getAccessController() /* mixed : accessController or false in none assigned */
    {
        return $this->appComponent->getAccessController();
    }

    /**
     * Assign an accessController to the app managed by this installer.
     * @param accessController $accessController The accessController to assign to the app managed by this installer.
     * @return bool True if the accessController was assigned to the app managed by this installer, false otherwise.
     */
    public function setAccessController(accessController $accessController): bool
    {
        return $this->appComponent->setAccessController($accessController);
    }

    /**
     * Get an array of the themes assigned to the app managed by this installer.
     * @return array Array of themes assigned to the app managed by this installer.
     */
    public function getThemes(): array
    {
        return $this->appComponent->getComponentAttributeValue('themes');
    }

    /**
     * Get an array of the dependencies assigned to the app managed by this installer.
     * @return array Array of dependencies assigned to the app managed by this installer.
     */
    public function getDependencies(): array
    {
        return $this->appComponent->getComponentAttributeValue('dependencies');
    }

    /**
     * Get an array of the custom attributes assigned to the app managed by this installer.
     * @return array Array of the custom attributes assigned to the app managed by this installer.
     */
    public function getCustomAttributes(): array
    {
        return $this->appComponent->getComponentAttributeValue('customAttributes');
    }

}
