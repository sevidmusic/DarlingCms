<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-19
 * Time: 09:54
 */

namespace DarlingCms\classes\observer\crud;

use DarlingCms\abstractions\privilege\APDOCompatibleAction;
use DarlingCms\abstractions\privilege\APDOCompatiblePermission;
use DarlingCms\classes\crud\MySqlActionCrud;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\privilege\Permission;
use DarlingCms\classes\staticClasses\core\CoreMySqlQuery;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\privilege\IPermission;
use SplSubject;

/**
 * Class MySqlActionCrudObserver/ This class is responsible for observing when a action is updated
 * via a MySqlActionCrud instance. This class will perform the required updates to any permissions
 * that would be affected by the changes to the original action.
 * @package DarlingCms\classes\observer\crud
 */
class MySqlActionCrudObserver implements \SplObserver
{
    /**
     * @var MySqlQuery Instance of a MySqlQuery implementation used to connect to the database where actions
     *                 and permissions or stored.
     */
    private $mySqlQuery;

    /**
     * @var MySqlPermissionCrud MySqlPermissionCrud implementation instance used to perform CRUD operations
     *                          on permissions effected by modification of the action.
     */
    private $permissionCrud;

    /**
     * @var array|IPermission[] Array of IPermission implementations for the all stored permissions as they
     *                          were were prior to modification of the action
     */
    private $permissions = array();


    /**
     * MySqlActionCrudObserver constructor. Instantiates the MySqlQuery and MySqlPermissionCrud instances.
     * Creates a record of all stored permissions as they were prior to modification of the action.
     */
    public function __construct()
    {
        $this->mySqlQuery = CoreMySqlQuery::DbConnection(CoreValues::getPrivilegesDBName());
        $this->permissionCrud = new MySqlPermissionCrud($this->mySqlQuery, new MySqlActionCrud($this->mySqlQuery, false)); // turn off observation for actionCrud instance to prevent infinite instantiation loop.
        /**
         * Since the action will may have already been modified when the update() method is called, create a record
         * of the permissions as they were prior to the modification of the action, this will allow this
         * class to determine which permissions will be affected by the modification of the relevant action.
         * This is done in the __construct() method to insure this information reflects the state of the action
         * and effected permissions prior to the action being modified.
         */
        foreach ($this->permissionCrud->readAll() as $permission) {
            array_push($this->permissions, $permission);
        }
    }


    /**
     * Receive update from an MySqlActionCrud implementation instance.
     * @param SplSubject $subject The MySqlActionCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            instance of the MySqlActionCrud implementation is passed to
     *                            the $subject parameter.
     * @return void
     */
    public function update(SplSubject $subject)
    {
        if (is_subclass_of($subject, 'DarlingCms\abstractions\crud\AMySqlActionCrud') && isset($subject->modType) === true && isset($subject->originalAction) === true && isset($subject->modifiedAction) === true) {
            foreach ($this->getEffectedPermissions($subject->originalAction) as $permission) {
                switch ($subject->modType) {
                    case  MySqlActionCrud::MOD_TYPE_UPDATE:
                    case  MySqlActionCrud::MOD_TYPE_DELETE:
                        $this->updatePermission($subject, $permission);
                        break;
                    default:
                        // Log error, invalid modification type
                        error_log('MySqlUserCrudObserver Error: Invalid modification type.');
                        break;
                }
            }
        } else {
            error_log('MySqlUserCrudObserver Error: Failed to update effected permissions for action ' . $subject->originalAction->getActionName() . '. WARNING: This may corrupt effected privileges. | Subject type was not valid.');
        }
    }

    /**
     * Performs the necessary updates to the specified permission.
     * @param SplSubject $subject The MySqlActionCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            instance of the MySqlActionCrud implementation is passed to
     *                            the $subject parameter.
     * @param APDOCompatiblePermission $permission The permission to update.
     * @return bool True if permission was updated, false otherwise.
     */
    private function updatePermission(SplSubject $subject, APDOCompatiblePermission $permission): bool
    {
        $actionsToPreserve = array();
        foreach ($permission->getActions() as $action) {
            // if same as original or modified move onto next action
            if ($subject->originalAction == $action || $subject->modifiedAction == $action) {
                continue;
            }
            array_push($actionsToPreserve, $action);
        }
        /**
         * If action was updated, add modified action to array of actions to preserve since this array will
         * be used to assign the appropriate actions to the new permission. This MUST not be done if action
         * was deleted because in that context the modified action should be removed from the permission, and
         * therefore excluded from the array of actions to preserve.
         */
        if ($subject->modType === MySqlActionCrud::MOD_TYPE_UPDATE) {
            array_push($actionsToPreserve, $subject->modifiedAction);
        }
        // create new permission, assigning actions to preserve and modified action
        $newPermission = new Permission($permission->getPermissionName(), $actionsToPreserve);
        // save new permission
        return $this->permissionCrud->update($permission->getPermissionName(), $newPermission);
    }

    /**
     * Returns the permissions that that will be affected by the updates to the Action
     * @param APDOCompatibleAction $action Instance of an APDOCompatibleAction implementation.
     * @return array|APDOCompatiblePermission[]
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
