<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-25
 * Time: 08:13
 */

namespace DarlingCms\classes\observer\crud;


use DarlingCms\classes\crud\MySqlActionCrud;
use SplObserver;
use SplSubject;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\interfaces\privilege\IRole;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\staticClasses\core\CoreMySqlObjectQuery;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\classes\privilege\Role;
use DarlingCms\abstractions\crud\AMySqlPermissionCrud;

/**
 * Class MySqlPermissionCrudObserver. This class is responsible for observing
 * when a permission is updated via a MySqlPermissionCrud instance. This class
 * will perform the required updates to any roles that would be affected by the
 * changes to the original permission.
 *
 * @package DarlingCms\classes\observer\crud
 *
 * @see \SplObserver
 * @see MySqlPermissionCrudObserver
 * @see MySqlPermissionCrudObserver::update()
 */
class MySqlPermissionCrudObserver implements SplObserver
{
    /**
     * @var MySqlObjectQuery Instance of a MySqlQuery implementation used to
     *                       connect to and query the database where IPermission
     *                       and IRole instance data is stored.
     */
    private $mySqlQuery;

    /**
     * @var MySqlRoleCrud MySqlRoleCrud implementation instance used to perform
     *                    CRUD operations on roles effected by modification of
     *                    a permission.
     */
    private $roleCrud;

    /**
     * @var array|IRole[] Array of IRole implementations for the all stored roles
     *                    as they were were prior to modification of a permission.
     */
    private $roles = array();


    /**
     * MySqlPermissionCrudObserver constructor. Instantiates the MyObjectSqlQuery and
     * MySqlRoleCrud instances, and creates a record of all stored roles as they
     * were prior to modification of the relevant permission.
     */
    public function __construct()
    {
        $this->mySqlQuery = CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName());
        $this->roleCrud = new MySqlRoleCrud($this->mySqlQuery, new MySqlPermissionCrud($this->mySqlQuery, new MySqlActionCrud($this->mySqlQuery, false), false)); // turn off observation for MySqlActionCrud and MySqlPermissionCrud instances to prevent infinite instantiation loop.
        /**
         * Create a record of all stored roles as they were prior to
         * modification of the relevant permission.
         */
        foreach ($this->roleCrud->readAll() as $role) {
            array_push($this->roles, $role);
        }
    }

    /**
     * Receive update from an MySqlPermissionCrud implementation instance.
     *
     * @param SplSubject $subject The MySqlPermissionCrud implementation
     *                            instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface
     *                            the $subject parameter will accept any instance of
     *                            an SplSubject, however, this method will only perform
     *                            as intended if an instance of the MySqlPermissionCrud
     *                            implementation is passed to the $subject parameter.
     * @return void
     */
    public function update(SplSubject $subject): void
    {
        /** @devNote: IDEs may complain that the performAppropriateUpdates() method
         *            expects an instance of a MySqlPermissionCrud implementation, it's
         *            okay, all MySqlPermissionCrud implementations implement the SplSubject
         *            interface, i.e. they fulfill both the SplSubject type requirement
         *            of the update() method as well as the MySqlPermissionCrud type
         *            requirement of the performAppropriateUpdates() method.
         *
         *            If only PHP allowed union types:
         *
         * function(TypeHintA | TypeHintB $param){}
         *
         *            Then the  performAppropriateUpdates() method could have been
         *            defined as:
         *
         * function performAppropriateUpdates(SplSubject | MySqlPermissionCrud $subject):void {...}
         *
         *            If somehow the supplied $subject is an instance of the SplSubject
         *            interface, but not an an instance of the MySqlPermissionCrud class, PHP
         *            will catch it since the performAppropriateUpdates() method explicitly
         *            expects a MySqlPermissionCrud implementation instance be passed to it's
         *            first parameter.
         *
         *            WARNING: Such an error would be need to be investigated as it could
         *                     be a sign of a security vulnerability.
         */
        if ($this->performAppropriateUpdates($subject) === false) {
            error_log('MySqlPermissionCrudObserver Error: Failed to perform all appropriate updates, some role data may have been corrupted!');
        }
    }

    /**
     * Updates any roles that are effected by changes to the relevant permission.
     *
     * @param MySqlPermissionCrud $mySqlPermissionCrud The MySqlPermissionCrud
     *                                                 implementation instance
     *                                                 that notified this observer.
     *
     * @return bool True if all appropriate updates were successful, false otherwise.
     */
    private function performAppropriateUpdates(MySqlPermissionCrud $mySqlPermissionCrud): bool
    {
        $status = array();
        if (isset($mySqlPermissionCrud->modType) === false || isset($mySqlPermissionCrud->originalPermission) === false || isset($mySqlPermissionCrud->modifiedPermission) === false) {
            error_log(sprintf("MySqlPermissionCrudObserver Error: Failed to update effected roles for permission %s. WARNING: This may corrupt effected privileges. | Subject type was not valid.", $mySqlPermissionCrud->originalPermission->getPermissionName()));
            return false;
        }
        foreach ($this->getEffectedRoles($mySqlPermissionCrud->originalPermission) as $role) {
            switch ($mySqlPermissionCrud->modType) {
                case  MySqlPermissionCrud::MOD_TYPE_UPDATE:
                case  MySqlPermissionCrud::MOD_TYPE_DELETE:
                    if ($this->updateRole($mySqlPermissionCrud, $role) === false) {
                        error_log('Failed to update role ' . $role->getRoleName() . '. WARNING: The role may have been corrupted, and may need to be re-defined.');
                        array_push($status, false);
                    }
                    break;
                default:
                    // Log error, invalid modification type
                    error_log('MySqlPermissionCrudObserver Error: Invalid modification type.');
                    array_push($status, false);
                    break;
            }
        }
        return in_array(false, $status, true);
    }

    /**
     * Performs the necessary updates to the specified role.
     *
     * @param MySqlPermissionCrud $mySqlPermissionCrud The MySqlPermissionCrud
     *                                                 implementation instance
     *                                                 that issued the notice
     *                                                 to update.
     *
     * @param APDOCompatibleRole $role The APDOCompatibleRole instance that represents
     *                                 the role to update.
     *
     * @return bool True if role was updated, false otherwise.
     */
    private function updateRole(MySqlPermissionCrud $mySqlPermissionCrud, APDOCompatibleRole $role): bool
    {
        $permissionsToPreserve = array();
        foreach ($role->getPermissions() as $permission) {
            // if same as original or modified move onto next permission
            if ($mySqlPermissionCrud->originalPermission == $permission || $mySqlPermissionCrud->modifiedPermission == $permission) {
                continue;
            }
            array_push($permissionsToPreserve, $permission);
        }
        /**
         * If permission was updated, add modified permission to array of permissions
         * to preserve since this array will be used to assign the appropriate
         * permissions to the new role. This MUST not be done if permission
         * was deleted because in that context the modified permission should
         * be removed from the role, and therefore excluded from the array of
         * permissions to preserve.
         */
        if ($mySqlPermissionCrud->modType === MySqlPermissionCrud::MOD_TYPE_UPDATE) {
            array_push($permissionsToPreserve, $mySqlPermissionCrud->modifiedPermission);
        }
        // create new role, assigning permissions to preserve and modified permission
        $newRole = new Role($role->getRoleName(), $permissionsToPreserve);
        // save new role
        return $this->roleCrud->update($role->getRoleName(), $newRole);
    }

    /**
     * Returns the roles that that will be affected by the updates to the Permission.
     *
     * @param APDOCompatiblePermission $permission Instance of an APDOCompatiblePermission
     *                                             implementation that represents the
     *                                             permission being modified.
     *
     * @return array|APDOCompatibleRole[] Array of APDOCompatibleRole implementation
     *                                    instances for all stored roles that would
     *                                    be effected by changes to the relevant
     *                                    permission.
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
