<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 12/21/17
 * Time: 7:06 AM
 */

namespace DarlingCms\classes\accessControl;

/**
 * Class role. Defines a named access controller.
 * @package DarlingCms\classes\accessControl
 */
class role extends accessController
{
    /**
     * @var string The name of this role object.
     */
    private $roleName;

    /**
     * role constructor. Instantiates a role object and assigns it a name.
     * @param string $roleName A name to use for this role.
     */
    public function __construct(string $roleName)
    {
        $this->roleName = $roleName;
    }

    /**
     * Check if this role has a specified role.
     * @param string $roleName The name of the role to check for. (Note: This method will
     * return true if the name of this role object is passed as the $roleName. It will also
     * return true if the specified role is assigned to any of the role objects assigned to
     * this role object. For example, if a role called Admin is assigned a role called Manager,
     * and Manager is assigned a role called Editor, then Admin->hasRole(Editor) will return
     * true because Editor is assigned to Manager, and Manager is assigned to Admin. i.e., Admin
     * inherits the Editor role via the Manager role.)
     * @return bool True if this role has the specified role, false otherwise.
     */
    public function hasRole(string $roleName): bool
    {
        /* If specified $roleName matches this role's name, return true. */
        if ($this->getRoleName() === $roleName) {
            return true;
        }
        /* Check the role itself for the specified role, if this role has the specified role assigned
           to it, return true. */
        if (parent::hasRole($roleName) === true) {
            return true;
        }
        /* This role does not have the specified role assigned to it. Check the role objects assigned to this role
           to see if the specified role is assigned to any of them. */
        foreach ($this->getRoles() as $role) {
            if ($role->hasRole($roleName) === true) {
                return true;
            }
        }
        /* Neither this role, or any of the role objects assigned to it have the specified role. */
        return false;
    }

    /**
     * Returns the role's name.
     * @return string The name of the role.
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * Check if this role has a specified privilege. (Note: This method will return true if any of this
     * role's assigned role objects has the specified privilege assigned to it directly, or via one of
     * the assigned role's assigned role objects. i.e. if a role Admin is assigned a role Manager, and
     * Manager is assigned role Editor, and Editor has privilege Read, then Admin->hasPrivilege(Read)
     * will be true since Manager inherits the Read privilege from Editor, and Admin inherits the Read
     * privilege from Manager.)
     * @param string $privilegeName The name of the privilege to check for.
     * @return bool True if this access controller has the specified privilege, false otherwise.
     */
    public function hasPrivilege(string $privilegeName): bool
    {
        /* Check the role itself for specified privilege, if privilege exists return true. */
        if (parent::hasPrivilege($privilegeName) === true) {
            return true;
        }
        /* Role itself did not have privilege. Check the role's assigned role objects for specified privilege. */
        foreach ($this->getRoles() as $role) {
            if ($role->hasPrivilege($privilegeName) === true) {
                return true;
            }
        }
        /* This role, nor any of it's assigned role objects has the specified privilege, return false. */
        return false;
    }
}
