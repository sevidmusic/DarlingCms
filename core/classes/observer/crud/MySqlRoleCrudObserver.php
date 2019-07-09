<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-27
 * Time: 22:34
 */

namespace DarlingCms\classes\observer\crud;

use DarlingCms\abstractions\crud\AMySqlUserCrud;
use DarlingCms\abstractions\privilege\APDOCompatibleRole;
use DarlingCms\abstractions\user\APDOCompatibleUser;
use DarlingCms\classes\crud\MySqlActionCrud;
use DarlingCms\classes\crud\MySqlPermissionCrud;
use DarlingCms\classes\crud\MySqlRoleCrud;
use DarlingCms\classes\crud\MySqlUserCrud;
use DarlingCms\classes\staticClasses\core\CoreMySqlObjectQuery;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\user\User;
use SplObserver;
use SplSubject;
use DarlingCms\classes\database\SQL\MySqlObjectQuery;

/**
 * Class MySqlRoleCrudObserver. This class is responsible for observing when a role is updated
 * via an AMySqlRoleCrud implementation instance. This class will perform the required updates
 * to any users that would be affected by the changes to the original role.
 * @package DarlingCms\classes\observer\crud
 */
class MySqlRoleCrudObserver implements SplObserver
{
    /**
     * @var MySqlObjectQuery Instance of a MySqlQuery implementation used to connect to the database where users
     *                 are stored.
     */
    private $mySqlUserQuery;

    /**
     * @var MySqlObjectQuery Instance of a MySqlQuery implementation used to connect to the database where privileges
     *                 are stored (i.e., Actions, Permissions, Roles).
     */
    private $mySqlPrivilegesQuery;

    /**
     * @var AMySqlUserCrud Instance of an AMySqlRoleCrud implementation used to perform CRUD operations
     *                     on stored users.
     */
    private $userCrud;

    /**
     * @var array|APDOCompatibleUser[] Array of APDOCompatibleUser implementations for all stored users as they
     *                                 were were prior to modification of the role.
     */
    private $users = array();

    /**
     * MySqlRoleCrudObserver constructor. Instantiates the MySqlQuery instance used to connect to the
     * database where users are stored. Instantiates the MySqlQuery instance used to connect to the
     * database where privileges are stored (i.e., Actions, Permissions, and Roles). Instantiates
     * the AMySqlRoleCrud implementation instance used to perform CRUD operations on stored users.
     * Creates a record of all users as they were prior to modification of the role.
     */
    public function __construct()
    {
        $this->mySqlUserQuery = CoreMySqlObjectQuery::DbConnection(CoreValues::getUsersDBName());
        $this->mySqlPrivilegesQuery = CoreMySqlObjectQuery::DbConnection(CoreValues::getPrivilegesDBName());
        $this->userCrud = new MySqlUserCrud(
            $this->mySqlUserQuery,
            new MySqlRoleCrud(
                $this->mySqlPrivilegesQuery,
                new MySqlPermissionCrud(
                    $this->mySqlPrivilegesQuery,
                    new MySqlActionCrud(
                        $this->mySqlPrivilegesQuery,
                        false
                    ),
                    false
                ),
                false
            )
        );
        /**
         * Since the role will have already been modified when the update() method is called,
         * create a record of all users as they were prior to the modification of the role,
         * this will allow this class to determine which users will be affected by the modification
         * of the relevant role. This is done in the __construct() method to insure this information
         * reflects the state of the role and effected users prior to the role being modified.
         */
        foreach ($this->userCrud->readAll() as $user) {
            array_push($this->users, $user);
        }
    }


    /**
     * Receive update from an AMySqlRoleCrud implementation instance.
     * @param SplSubject $subject The AMySqlRoleCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            instance of an AMySqlRoleCrud implementation is passed to
     *                            the $subject parameter.
     * @return void
     */
    public function update(SplSubject $subject)
    {
        if (is_subclass_of($subject, 'DarlingCms\abstractions\crud\AMySqlRoleCrud') === true && isset($subject->modType) === true && isset($subject->originalRole) === true && isset($subject->modifiedRole) === true) {
            foreach ($this->getEffectedUsers($subject->originalRole) as $user) {
                switch ($subject->modType) {
                    case  MySqlPermissionCrud::MOD_TYPE_UPDATE:
                    case  MySqlPermissionCrud::MOD_TYPE_DELETE:
                        if ($this->updateUser($subject, $user) === false) {
                            error_log('Failed to update user ' . $user->getUserName() . '. WARNING: The user may have been corrupted, and may need to be re-created.');
                        }
                        break;
                    default:
                        // Log error, invalid modification type
                        error_log('MySqlUserCrudObserver Error: Invalid modification type.');
                        break;
                }
            }
        } else {
            error_log('MySqlRoleCrudObserver Error: Failed to update effected users for role ' . $subject->originalRole->getRoleName() .
                '. WARNING: This may corrupt effected users. | Subject type was not valid.');
        }
    }

    /**
     * Update the specified user.
     * @param SplSubject $subject The role that issued the notice to update.
     * @param APDOCompatibleUser $user An APDOCompatibleUser implementation instance the represents
     *                                 the user to update.
     * @return bool True if user was updated, false otherwise.
     */
    private function updateUser(SplSubject $subject, APDOCompatibleUser $user): bool
    {
        $rolesToPreserve = array();
        foreach ($user->getRoles() as $role) {
            // if same as original or modified move onto next role
            if ($subject->originalRole == $role || $subject->modifiedRole == $role) {
                continue;
            }
            array_push($rolesToPreserve, $role);
        }
        /**
         * If role was updated, add modified role to array of roles to preserve since this array
         * will be used to assign the appropriate roles to the new user. This MUST not be done if role
         * was deleted because in that context the modified role should be removed from the user, and
         * therefore excluded from the array of roles to preserve.
         */
        if ($subject->modType === MySqlRoleCrud::MOD_TYPE_UPDATE) {
            array_push($rolesToPreserve, $subject->modifiedRole);
        }
        // create new user, assigning roles to preserve and modified role
        $newUser = new User($user->getUserName(), [$user::USER_PUBLIC_META_INDEX => $user->getPublicMeta(), $user::USER_PRIVATE_META_INDEX => $user->getPrivateMeta()], $rolesToPreserve);
        // save new user
        return $this->userCrud->update($user->getUserName(), $newUser);
    }

    /**
     * Returns an array of the users that will be affected by modification of the role.
     * @param APDOCompatibleRole $role The role being modified.
     * @return array|APDOCompatibleUser[] Array of APDOCompatibleUser implementation instances that represent
     *                                    the users that will be effected by the modification of the role.
     */
    private function getEffectedUsers(APDOCompatibleRole $role): array
    {
        $effectedUsers = array();
        foreach ($this->users as $user) {
            if ($user->userHasRole($role) === true) {
                array_push($effectedUsers, $user);
            }
        }
        return $effectedUsers;
    }
}
