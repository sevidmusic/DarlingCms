<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-25
 * Time: 08:13
 */

namespace DarlingCms\classes\observer\crud;


use DarlingCms\classes\crud\MySqlActionCrud;
use SplSubject;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\staticClasses\core\CoreMySqlQuery;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\classes\privilege\Role;
use DarlingCms\abstractions\crud\AMySqlPermissionCrud;

/**
 * Class MySqlPermissionCrudObserver. This class is responsible for observing when a permission is updated
 * via a MySqlPermissionCrud instance. This class will perform the required updates to any roles
 * that would be affected by the changes to the original permission.
 * @package DarlingCms\classes\observer\crud
 */
class MySqlPermissionCrudObserver implements \SplObserver
{
    /**
     * @var MySqlQuery Instance of a MySqlQuery implementation used to connect to the database where permissions
     *                 and roles or stored.
     */
    private $mySqlQuery;

    /**
     * @var MySqlRoleCrud MySqlRoleCrud implementation instance used to perform CRUD operations
     *                    on roles effected by modification of the permission.
     */
    private $roleCrud;

    /**
     * @var array|IRole[] Array of IRole implementations for the all stored roles as they
     *                          were were prior to modification of the permission
     */
    private $roles = array();


    /**
     * MySqlPermissionCrudObserver constructor. Instantiates the MySqlQuery and MySqlRoleCrud instances.
     * Creates a record of all stored roles as they were prior to modification of the permission.
     */
    public function __construct()
    {
        $this->mySqlQuery = CoreMySqlQuery::DbConnection(CoreValues::getPrivilegesDBName());
        $this->roleCrud = new MySqlRoleCrud($this->mySqlQuery, new MySqlPermissionCrud($this->mySqlQuery, new MySqlActionCrud($this->mySqlQuery, false), false)); // turn off observation for MySqlActionCrud and MySqlPermissionCrud instances to prevent infinite instantiation loop.
        /**
         * Since the permission will have already been modified when the update() method is called, create a record
         * of the roles as they were prior to the modification of the permission, this will allow this
         * class to determine which roles will be affected by the modification of the relevant permission.
         * This is done in the __construct() method to insure this information reflects the state of the permission
         * and effected roles prior to the permission being modified.
         */
        foreach ($this->roleCrud->readAll() as $role) {
            array_push($this->roles, $role);
        }
    }


    /**
     * Receive update from an MySqlPermissionCrud implementation instance.
     * @param SplSubject $subject The MySqlPermissionCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            instance of the MySqlPermissionCrud implementation is passed to
     *                            the $subject parameter.
     * @return void
     */
    public function update(SplSubject $subject): void
    {
        if (is_subclass_of($subject, 'DarlingCms\abstractions\crud\AMySqlPermissionCrud') === true && isset($subject->modType) === true && isset($subject->originalPermission) === true && isset($subject->modifiedPermission) === true) {
            foreach ($this->getEffectedRoles($subject->originalPermission) as $role) {
                switch ($subject->modType) {
                    case  MySqlPermissionCrud::MOD_TYPE_UPDATE:
                    case  MySqlPermissionCrud::MOD_TYPE_DELETE:
                        if($this->updateRole($subject, $role) === false){
                            error_log('Failed to update role ' . $role->getRoleName().'. WARNING: The role may have been corrupted, and may need to be re-defined.');
                        }
                        break;
                    default:
                        // Log error, invalid modification type
                        error_log('MySqlUserCrudObserver Error: Invalid modification type.');
                        break;
                }
            }
        } else {
            error_log('MySqlUserCrudObserver Error: Failed to update effected roles for permission ' . $subject->originalPermission->getPermissionName() .
                '. WARNING: This may corrupt effected privileges. | Subject type was not valid.');
        }
    }

    /**
     * Performs the necessary updates to the specified role.
     * @param SplSubject $subject The MySqlPermissionCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            instance of the MySqlPermissionCrud implementation is passed to
     *                            the $subject parameter.
     * @param APDOCompatibleRole $role The role to update.
     * @return bool True if role was updated, false otherwise.
     */
    private function updateRole(SplSubject $subject, APDOCompatibleRole $role): bool
    {
        $permissionsToPreserve = array();
        foreach ($role->getPermissions() as $permission) {
            // if same as original or modified move onto next permission
            if ($subject->originalPermission == $permission || $subject->modifiedPermission == $permission) {
                continue;
            }
            array_push($permissionsToPreserve, $permission);
        }
        /**
         * If permission was updated, add modified permission to array of permissions to preserve since this array
         * will be used to assign the appropriate permissions to the new role. This MUST not be done if permission
         * was deleted because in that context the modified permission should be removed from the role, and
         * therefore excluded from the array of permissions to preserve.
         */
        if ($subject->modType === MySqlPermissionCrud::MOD_TYPE_UPDATE) {
            array_push($permissionsToPreserve, $subject->modifiedPermission);
        }
        // create new role, assigning permissions to preserve and modified permission
        $newRole = new Role($role->getRoleName(), $permissionsToPreserve);
        // save new role
        return $this->roleCrud->update($role->getRoleName(), $newRole);
    }

    /**
     * Returns the roles that that will be affected by the updates to the Permission
     * @param APDOCompatiblePermission $permission Instance of an APDOCompatiblePermission implementation.
     * @return array|APDOCompatibleRole[]
     */
    private function getEffectedRoles(APDOCompatiblePermission $permission): array
    {
        $effectedRoles = array();
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission) === true) {
                array_push($effectedRoles, $role);
            }
        }
        return $effectedRoles;
    }

}
