<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 2019-02-17
 * Time: 15:04
 */

namespace DarlingCms\abstractions\accessControl;


use DarlingCms\classes\accessControl\UserLogin;
use DarlingCms\classes\factory\CoreMySqlCrudFactory;
use DarlingCms\interfaces\accessControl\IAppConfig;

/**
 * Class AAdminAppConfig. Defines an abstract implementation of the IAppConfig interface that can be
 * used by apps that wish to restrict access to users that are assigned administrative roles.
 * @package DarlingCms\abstractions\accessControl
 */
abstract class AAdminAppConfig implements IAppConfig
{

    protected $userLogin;
    protected $crudFactory;
    protected $userName;
    protected $userCrud;
    protected $roleCrud;
    protected $user;
    protected $validRoles;

    /**
     * AAdminAppConfig constructor. Sets the class properties.
     */
    final public function __construct()// @todo ! Consider if this should really be final! Having this be final does insure all implementations initialize the required props, but it prevents apps from defining additional validation that may be unique to the app, for instance an app may only show up on certain pages, and may wish to add such a check to it's validation logic...
    {
        $this->userLogin = new UserLogin();
        $this->crudFactory = new CoreMySqlCrudFactory();
        $this->userName = (empty($this->userLogin->read(UserLogin::CURRENT_USER_POST_VAR_NAME)) === false ? $this->userLogin->read(UserLogin::CURRENT_USER_POST_VAR_NAME) : '');
        $this->userCrud = $this->crudFactory->getUserCrud();
        $this->roleCrud = $this->crudFactory->getRoleCrud();
        $this->user = $this->userCrud->read($this->userName);
        $this->validRoles = $this->defineValidRoles();
    }


    /**
     * Validates access. This implementation verifies that the currently logged in user is assigned
     * the correct role(s) to use this app. This method will return true only if a user is logged in,
     * and said user is assigned the required role(s).
     * @return bool True if access is valid, false otherwise.
     */
    final public function validateAccess(): bool
    {
        if ($this->userLogin->isLoggedIn($this->userName) === true && $this->hasValidRoles() === true) {
            return true;
        }
        return false;
    }

    /**
     * Determines if the current user has the appropriate roles to use this app.
     * @return bool True if user is assigned ALL of the required roles, false otherwise.
     */
    protected function hasValidRoles()
    {
        $status = array();
        foreach ($this->validRoles as $roleName) {
            $role = $this->roleCrud->read($roleName); // @todo need to verify role exists or else errors may occur.
            array_push($status, $this->user->userHasRole($role));
        }
        return !empty($status) && !in_array(false, $status, true);
    }

    /**
     * Gets the app's name.
     * @return string The app's name.
     */
    abstract public function getName(): string;

    /**
     * Gets an array of the names of the themes assigned to the app.
     * @return array Array of the names of the themes assigned to the app.
     */
    abstract public function getThemeNames(): array;

    /**
     * Gets an array of the names of the javascript libraries assigned to the app.
     * @return array Array of the names of the javascript libraries assigned to the app.
     */
    abstract public function getJsLibraryNames(): array;

    /**
     * Returns an array of the names of the Roles that are required by this app.
     * ALL IMPLEMENTATIONS OF THIS CLASS MUST IMPLEMENT THIS METHOD!
     * @return array Array of the names of the Roles that are required by this app.
     */
    abstract protected function defineValidRoles(): array;


}
