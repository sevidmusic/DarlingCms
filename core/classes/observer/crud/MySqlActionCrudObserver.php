<?php
/**
 * Created by Sevi Darling.
 * Date: 2019-02-19
 * Time: 09:54
 */

namespace DarlingCms\classes\observer\crud;

use DarlingCms\abstractions\privilege\APDOCompatibleAction;
use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\classes\crud\MySqlActionCrud;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\classes\staticClasses\core\CoreMySqlObjectQuery;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\privilege\IPermission;
use SplObserver;
use SplSubject;

/**
 * Class MySqlActionCrudObserver This class is responsible for observing when a
 * action is updated via a MySqlActionCrud instance. This class will perform the
 * required updates to any stored IPermission instance data that would be affected
 * by the changes to stored stored IAction instance data.
 *
 * @package DarlingCms\classes\observer\crud
 *
 * @see \SplObserver
 * @see MySqlActionCrudObserver
 * @see MySqlActionCrudObserver::update()
 */
class MySqlActionCrudObserver implements SplObserver
{
    /**
     * @var MySqlObjectQuery Instance of a MySqlObjectQuery implementation used to connect
     *                       to the database where actions and permissions or stored.
     */
    private $mySqlObjectQuery;

    /**
     * @var MySqlPermissionCrud MySqlPermissionCrud implementation instance used to
     *                          perform CRUD operations on permissions effected by
     *                          modification of a action.
     */
    private $permissionCrud;

    /**
     * @var array|IPermission[] Array of IPermission implementations for the all stored
     *                          permissions as they were were prior to modification of
     *                          a action.
     */
    private $permissions = array();


    /**
     * MySqlActionCrudObserver constructor. Instantiates the MySqlObjectQuery and
     * MySqlPermissionCrud instances. Creates a record of all stored permissions
     * as they were prior to modification of a action.
     */
    public function __construct()
    {
        /**
         * Use the CoreMySqlObjectQuery static factory class to obtain an
         * appropriate MySqlObjectQuery implementation instance to assign
         * to the $mySqlObjectQuery property.
         */
        $this->mySqlObjectQuery = CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName());
        /**
         * Turn off observation for actionCrud instance to prevent infinite
         * instantiation loop.
         */
        $this->permissionCrud = new MySqlPermissionCrud($this->mySqlObjectQuery, new MySqlActionCrud($this->mySqlObjectQuery, false));
        /**
         * Create a record of all stored IPermissions instances as they were prior
         * to the modification of a action, this will allow this class to determine
         * which permissions will be affected by changes to the relevant action.
         */
        foreach ($this->permissionCrud->readAll() as $permission) {
            array_push($this->permissions, $permission);
        }
    }


    /**
     * Updates effected IPermission instances on notice from the relevant MySqlActionCrud
     * implementation instance.
     *
     * @param SplSubject $subject The MySqlActionCrud implementation instance that issued
     *                            the notice.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an
     *                            SplSubject, however, this method will only perform as
     *                            intended if an instance of the MySqlActionCrud
     *                            implementation is passed to the $subject parameter.
     *
     * @return void
     */
    public function update(SplSubject $subject)
    {
        /** @devNote: IDEs may complain that the performAppropriateUpdates() method
         *            expects an instance of a MySqlActionCrud implementation, it's
         *            okay, all MySqlActionCrud implementations implement the SplSubject
         *            interface, i.e. they fulfill both the SplSubject type requirement
         *            of the update() method as well as the MySqlActionCrud type
         *            requirement of the performAppropriateUpdates() method.
         *
         *            If only PHP allowed union types:
         *
         * function(TypeHintA | TypeHintB $param){}
         *
         *            Then the  performAppropriateUpdates() method could have been
         *            defined as:
         *
         * function performAppropriateUpdates(SplSubject | MySqlActionCrud $subject):void {...}
         *
         *            If somehow the supplied $subject is an instance of the SplSubject
         *            interface, but not an an instance of the MySqlActionCrud class, PHP
         *            will catch it since the performAppropriateUpdates() method explicitly
         *            expects a MySqlActionCrud implementation instance be passed to it's
         *            first parameter.
         *
         *            WARNING: Such an error would be need to be investigated as it could
         *                     be a sign of a security vulnerability.
         */
        $this->performAppropriateUpdates($subject);
    }

    /**
     * Updates any permissions that are effected by changes to the relevant action.
     *
     * @param MySqlActionCrud $mySqlActionCrud The MySqlActionCrud implementation instance
     *                                         that notified this observer.
     */
    private function performAppropriateUpdates(MySqlActionCrud $mySqlActionCrud): void
    {
        if (isset($subject->modType) === false || isset($subject->originalAction) === false || isset($subject->modifiedAction) === false) {
            error_log('MySqlUserCrudObserver Error: Failed to update effected permissions for Action "' . $mySqlActionCrud->originalAction->getActionName() . '". WARNING: This may corrupt effected privileges.');
        }
        foreach ($this->getEffectedPermissions($mySqlActionCrud->originalAction) as $permission) {
            switch ($mySqlActionCrud->modType) {
                case  MySqlActionCrud::MOD_TYPE_UPDATE:
                case  MySqlActionCrud::MOD_TYPE_DELETE:
                    $this->updatePermission($mySqlActionCrud, $permission);
                    break;
                default:
                    // Log error, invalid modification type
                    error_log('MySqlUserCrudObserver Error: The "' . $permission->getPermissionName() . '" Permission could not be updated to reflect changes to the "' . $mySqlActionCrud->originalAction->getActionName() . '" Action because the supplied modification type was invalid.');
                    break;
            }
        }
    }

    /**
     * Performs appropriate updates to the specified permission.
     *
     * @param MySqlActionCrud $mySqlActionCrud The MySqlActionCrud implementation
     *                                         instance that issued the notice to
     *                                         update the permission.
     *
     * @param APDOCompatiblePermission $permission The permission to update.
     *
     * @return bool True if permission was updated, false otherwise.
     */
    private function updatePermission(MySqlActionCrud $mySqlActionCrud, APDOCompatiblePermission $permission): bool
    {
        $actionsToPreserve = array();
        foreach ($permission->getActions() as $action) {
            // if same as original or modified move onto next action | note: == used is best when comparing different instances
            if ($mySqlActionCrud->originalAction == $action || $mySqlActionCrud->modifiedAction == $action) {
                continue;
            }
            array_push($actionsToPreserve, $action);
        }
        /**
         * If action was updated, add modified action to array of actions to preserve
         * since this array will be used to assign the appropriate actions to the new
         * permission. This MUST not be done if action was deleted because in that
         * context the modified action should be removed from the permission, and
         * therefore excluded from the array of actions to preserve.
         */
        if ($mySqlActionCrud->modType === MySqlActionCrud::MOD_TYPE_UPDATE) {
            array_push($actionsToPreserve, $mySqlActionCrud->modifiedAction);
        }
        // create new permission, assigning actions to preserve and modified action
        $newPermission = new Permission($permission->getPermissionName(), $actionsToPreserve);
        // save new permission
        return $this->permissionCrud->update($permission->getPermissionName(), $newPermission);
    }

    /**
     * Returns an array of APDOCompatiblePermission implementation instances
     * for all the Permissions that will be affected by the changes to the Action.
     *
     * @param APDOCompatibleAction $action Instance of an APDOCompatibleAction
     *                                     implementation that represents the
     *                                     Action that was changed.
     *
     * @return array|APDOCompatiblePermission[] Array if APDOCompatiblePermission
     *                                          implementation instances for all
     *                                          the Permissions that would be
     *                                          effected by changes to the Action.
     */
    private function getEffectedPermissions(APDOCompatibleAction $action): array
    {
        $effectedPermissions = array();
        foreach ($this->permissions as $permission) {
            if ($permission->hasAction($action) === true) {
                array_push($effectedPermissions, $permission);
            }
        }
        return $effectedPermissions;
    }
}
