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
    final public function __construct()
    {
        $this->userLogin = new UserLogin();
        $this->crudFactory = new CoreMySqlCrudFactory();
        $this->userName = (empty($this->userLogin->read(UserLogin::CURRENT_USER_POST_VAR_NAME)) === false ? $this->userLogin->read(UserLogin::CURRENT_USER_POST_VAR_NAME) : '');
        $this->userCrud = $this->crudFactory->getUserCrud();
        $this->roleCrud = $this->crudFactory->getRoleCrud();
        /* @todo: The ternary operator is a temp fix for the following error that keeps occurring: "PHP Fatal error: Uncaught TypeError: Argument 1 passed to DarlingCms\classes\crud\MySqlUserCrud::read() must be of the type string, null given, called in /Applications/MAMP/htdocs/DarlingCms/core/abstractions/accessControl/AAdminAppConfig.php on line 41 and defined in /Applications/MAMP/htdocs/DarlingCms/core/classes/crud/MySqlUserCrud.php:55" | Original code: $this->user = $this->userCrud->read($this->userName); // this code causes errors, somehow a null value makes it's way here it seems whenever a session has timed out. */
        $this->user = ($this->userCrud->read(empty($this->userName) === false ? $this->userName : ''));
        $this->validRoles = $this->defineValidRoles();
    }


    /**
     * Validates access. This implementation verifies that the currently logged in user is assigned
     * the correct role(s) to use this app. This method will return true only if a user is logged in,
     * and said user is assigned the required role(s). Also note, this method will check if the user
     * is in the process of being logged in or out and will return false in that context.
     * @return bool True if access is valid, false otherwise.
     */
    final public function validateAccess(): bool
    {
        // Always return false if user name is empty.
        if (empty($this->userName) === true) {
            // error_log("AAdminAppConfig Error: Unable to determine current user's username.");
            return false;
        }
        $loggingIn = (empty(filter_input(INPUT_POST, $this->userLogin::LOGIN_STATE_VAR_NAME)) === true ? false : filter_input(INPUT_POST, $this->userLogin::LOGIN_STATE_VAR_NAME) === $this->userLogin::LOGIN_STATE_VAR_VALUE);
        $loggingOut = (empty(filter_input(INPUT_POST, $this->userLogin::LOGOUT_STATE_VAR_NAME)) === true ? false : filter_input(INPUT_POST, $this->userLogin::LOGOUT_STATE_VAR_NAME) === $this->userLogin::LOGOUT_STATE_VAR_VALUE);
        if ($loggingIn === false && $loggingOut === false && $this->userLogin->isLoggedIn($this->userName) === true && $this->hasValidRoles() === true) {
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
