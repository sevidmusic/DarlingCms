<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 12/11/17
 * Time: 6:59 AM
 */

namespace DarlingCms\classes\accessControl;

/**
 * Class accessController. Defines an object that provides access control via defined roles and privileges.
 * @package DarlingCms\classes\accessControl
 */
class accessController
{
    /**
     * @var array Array of roles assigned to this access controller.
     */
    private $roles = array();

    /**
     * @var array Array of privileges assigned to this access controller.
     */
    private $privileges = array();

    /**
     * Assign a role to this access controller.
     * @param role $role The role instance to assign to this access controller.
     * @return bool True if role was assigned successfully, false otherwise.
     */
    public function assignRole(\DarlingCms\classes\accessControl\role $role): bool
    {
        $this->roles[$role->getRoleName()] = $role;
        return isset($this->roles[$role->getRoleName()]);
    }

    /**
     * Remove a role assigned to this access controller.
     * @param string $roleName The name of the role to remove from this access controller.
     * @return bool True if role was removed successfully, false otherwise.
     */
    public function removeRole(string $roleName): bool
    {
        unset($this->roles[$roleName]);
        return !isset($this->roles[$roleName]);
    }

    /**
     * Assign a privilege to this access controller.
     * @param privilege $privilege The privilege instance to assign to this access controller.
     * @return bool True if privilege was assigned successfully, false otherwise.
     */
    public function assignPrivilege(\DarlingCms\classes\accessControl\privilege $privilege): bool
    {
        $this->privileges[$privilege->getPrivilegeName()] = $privilege;
        return isset($this->privileges[$privilege->getPrivilegeName()]);

    }

    /**
     * Remove a privilege assigned to this access controller.
     * @param string $privilegeName The name of the privilege to remove from this access controller.
     * @return bool True if privilege was removed successfully, false otherwise.
     */
    public function removePrivilege(string $privilegeName): bool
    {
        unset($this->privileges[$privilegeName]);
        return !isset($this->privileges[$privilegeName]);
    }

    /**
     * Validate if a specified accessController object has an appropriate role or privilege
     * to access this accessController. i.e., Determine if specified accessController object
     * has at least one role or privilege that matches one of the role or privilege objects
     * assigned to this accessController.
     * @param accessController $accessController Access controller to validate.
     * @return bool True if specified accessController has at least one role or
     *              privilege that matches one of the role or privilege objects
     *              assigned to this accessController, false otherwise.
     */
    public function validateAccess(accessController $accessController)
    {
        /* Check for valid role. */
        foreach (array_keys($this->getRoles()) as $role) {
            if ($accessController->hasRole($role)) {
                return true;
            }
        }
        /* No valid role, check for valid privilege. */
        foreach (array_keys($this->getPrivileges()) as $privilege) {
            if ($accessController->hasPrivilege($privilege)) {
                return true;
            }
        }
        /* Controller does not have any valid roles or privileges. */
        return false;
    }

    /**
     * Returns an array of roles assigned to this access controller.
     * @return array The roles array.
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Check if this access controller has a specified role.
     * @param string $roleName The name of the role to check for.
     * @return bool True if this access controller has the specified role, false otherwise.
     */
    public function hasRole(string $roleName): bool
    {
        if (in_array($roleName, array_keys($this->getRoles()), true)) {
            return true;
        }
        return false;
    }

    /**
     * Returns an array of roles assigned to this access controller.
     * @return array The privileges array.
     */
    public function getPrivileges(): array
    {
        return $this->privileges;
    }

    /**
     * Check if this access controller has a specified privilege.
     * @param string $privilegeName The name of the privilege to check for.
     * @return bool True if this access controller has the specified privilege, false otherwise.
     */
    public function hasPrivilege(string $privilegeName): bool
    {
        if (in_array($privilegeName, array_keys($this->getPrivileges()), true)) {
            return true;
        }
        return false;
    }
}
