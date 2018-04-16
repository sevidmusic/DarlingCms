<?php

namespace DarlingCms\classes\installer;


use DarlingCms\abstractions\crud\AregisteredCrud;
use DarlingCms\classes\accessControl\accessController;
use DarlingCms\classes\component\app;
use DarlingCms\classes\component\html\htmlHead;
use DarlingCms\interfaces\installer\Iinstaller;

/**
 * Class appInstaller. Manages installation and un-installation of a specified app.
 * @package DarlingCms\classes\installer
 * @see \DarlingCms\abstractions\crud\AregisteredCrud
 * @see \DarlingCms\classes\component\app
 * @see \DarlingCms\classes\component\html\htmlHead
 */
class appInstaller implements Iinstaller
{
    /**
     * @var string The name of the app this installer manages.
     */
    private $appName;

    /**
     * @var AregisteredCrud The AregisteredCrud implementation instance assigned to this installer. This
     *                      object is used by the installer to interact with storage.
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     */
    private $crud;

    /**
     * @var app The app object instance for the app managed by this installer. This object is used
     *          by the installer to configure the app managed by this installer.
     * @see \DarlingCms\classes\component\app
     */
    private $appComponent;

    /**
     * @var htmlHead The htmlHead object used by this installer. This object is used to add stylesheets,
     *               scripts, and meta tags to the html head on behalf of the app managed by this installer.
     * @see \DarlingCms\classes\component\html\htmlHead
     */
    private $htmlHead;

    /**
     * appInstaller constructor. Assigns the specified $appName to the $appName property. Assigns the specified
     * AregisteredCrud implementation instance to the $crud property. Instantiates an app object for the app managed
     * by this installer and assigns it to the $appComponent property. Instantiates an htmlHead object for
     * the installer and assigns it the htmlHead property.
     * @param string $appName The name of the app this installer manages.
     * @param AregisteredCrud $crud The AregisteredCrud implementation instance to assign to this installer.
     * @param array $customAttributes Array of custom attributes to pass to the app object instantiated for
     *                                the app managed by this installer.
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     * @see \DarlingCms\classes\component\app
     * @see \DarlingCms\classes\component\html\htmlHead
     */
    public function __construct(string $appName, AregisteredCrud $crud, array $customAttributes = array())
    {
        /* Assign the specified $appName to the $appName property. */
        $this->appName = $appName;
        /* Assign the specified AregisteredCrud implementation instance to the $crud property. */
        $this->crud = $crud;
        /* Instantiate an app object for the app this installer manages, and assign it to the $appComponent property. */
        $this->appComponent = new app($appName, $customAttributes);
        /* Instantiate an htmlHead object for this installer and assign it to the $htmlHead property. */
        $this->htmlHead = new htmlHead($this->crud);
    }

    /**
     * Perform installation. This method stores the app object for the app managed by this installer.
     * @return bool Return true on success, false on failure.
     * @see \DarlingCms\classes\component\app
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     */
    public function install(): bool
    {
        /* Store the app object. */
        return $this->crud->create($this->appName, $this->appComponent);
    }

    /**
     * Perform un-installation.
     *
     * WARNING: This method will delete the app object for the app managed by this installer from storage.
     * @return bool Return true on success, false on failure.
     * @see \DarlingCms\classes\component\app
     * @see \DarlingCms\abstractions\crud\AregisteredCrud
     */
    public function unInstall(): bool
    {
        /* Delete the app's stored app object. */
        return $this->crud->delete($this->appName);
    }

    /**
     * Enable the app managed by this installer. Specifically, sets the app's app object's
     * enabled attribute to true.
     *
     * WARNING: This method must be called before the install() method to have an effect as it does not
     * act directly on the app's stored app object. Calling this method after the install() method will
     * have no effect on the app's stored app object, i.e., the app's stored app object will still be
     * configured as disabled.
     *
     * Note: This method will also enable any themes registered with the app's app object via the htmlHead object
     * assigned to the $htmlHead property.
     * @return bool True if app was enabled, false otherwise.
     * @see \DarlingCms\classes\component\app::enableApp()
     * @see appInstaller::getThemes()
     * @see \DarlingCms\classes\component\html\htmlHead::enableTheme()
     */
    public function enableApp()
    {
        /* Initialize the status array, which will track the success or failure of the method calls in this
           method's logic. */
        $status = array();
        /* Enable the app. */
        array_push($status, $this->appComponent->enableApp());
        /* Enable any themes registered with the app. */
        foreach ($this->getThemes() as $theme) {
            array_push($status, $this->htmlHead->enableTheme($theme));
        }
        /* Return true if the app, and it's themes, were enabled, false otherwise. */
        return !in_array(false, $status, true);
    }

    /**
     * Register a dependency for the app managed by this installer.
     * @param string $dependency The name of an app to register as a dependency, i.e., the name
     *                           of an app the app managed by this installer depends on.
     * Note: An app cannot register itself as a dependency, attempting to register
     * the app managed by this installer as a dependency will cause this method
     * to return false.
     * @return bool True if dependency was registered, false otherwise.
     * @see \DarlingCms\classes\component\app::registerDependency()
     */
    public function registerDependency(string $dependency): bool
    {
        return $this->appComponent->registerDependency($dependency);
    }

    /**
     * Register a theme for the app managed by this installer.
     * @param string $theme The name of the theme to register.
     * @return bool True if theme was registered, false otherwise.
     * @see \DarlingCms\classes\component\app::registerTheme()
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
     * @see \DarlingCms\classes\component\app::setCustomAttribute()
     */
    public function setCustomAttribute(string $customAttributeKey, $customAttributeValue): bool
    {
        return $this->appComponent->setCustomAttribute($customAttributeKey, $customAttributeValue);
    }

    /**
     * Determines if the app managed by this installer is assigned an accessController.
     * @return bool True if the app managed by this installer is assigned an accessController, false otherwise.
     * @see \DarlingCms\classes\component\app::hasAccessController()
     */
    public function hasAccessController(): bool
    {
        return $this->appComponent->hasAccessController();
    }

    /**
     * Returns the accessController assigned to the app managed by this installer, or false
     * if the app managed by this installer an is not assigned an accessController.
     * @return bool|\DarlingCms\classes\accessControl\accessController The accessController assigned to the
     *                                                                 app managed by this installer, or false
     *                                                                 if an accessController is not assigned to
     *                                                                 the app managed by this installer.
     * @see \DarlingCms\classes\component\app::getAccessController()
     */
    public function getAccessController()
    {
        return $this->appComponent->getAccessController();
    }

    /**
     * Assign an accessController to the app managed by this installer.
     * @param accessController $accessController The accessController instance to assign to the app managed by this
     *                                           installer.
     * @return bool True if the accessController was assigned to the app managed by this installer, false otherwise.
     * @see \DarlingCms\classes\component\app::setAccessController()
     */
    public function setAccessController(accessController $accessController): bool
    {
        return $this->appComponent->setAccessController($accessController);
    }

    /**
     * Get an array of the themes assigned to the app managed by this installer.
     * @return array Array of themes assigned to the app managed by this installer.
     * @see \DarlingCms\classes\component\app::getComponentAttributeValue()
     */
    public function getThemes(): array
    {
        return $this->appComponent->getComponentAttributeValue('themes');
    }

    /**
     * Get an array of the dependencies assigned to the app managed by this installer.
     * @return array Array of dependencies assigned to the app managed by this installer.
     * @see \DarlingCms\classes\component\app::getComponentAttributeValue()
     */
    public function getDependencies(): array
    {
        return $this->appComponent->getComponentAttributeValue('dependencies');
    }

    /**
     * Get an array of the custom attributes assigned to the app managed by this installer.
     * @return array Array of the custom attributes assigned to the app managed by this installer.
     * @see \DarlingCms\classes\component\app::getComponentAttributeValue()
     */
    public function getCustomAttributes(): array
    {
        return $this->appComponent->getComponentAttributeValue('customAttributes');
    }

}
