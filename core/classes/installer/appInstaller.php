<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/29/18
 * Time: 7:18 PM
 */

namespace DarlingCms\classes\installer;


use DarlingCms\abstractions\crud\AregisteredCrud;
use DarlingCms\classes\component\app;
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

}
