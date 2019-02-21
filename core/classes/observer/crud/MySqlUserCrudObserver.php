<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-02-08
 * Time: 01:43
 */

namespace DarlingCms\classes\observer\crud;


use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\staticClasses\core\CoreMySqlQuery;
use DarlingCms\classes\staticClasses\core\CoreValues;
use SplSubject;
use DarlingCms\abstractions\crud\AMySqlUserCrud;

/**
 * Class MySqlUserCrudObserver. This class is responsible for observing when a user is updated
 * via an AMySqlUserCrud implementation instance. This class will perform the required updates
 * to the user's corresponding password data whenever a user is updated.
 * @package DarlingCms\classes\observer\crud
 */
class MySqlUserCrudObserver implements \SplObserver
{

    /**
     * @var MySqlQuery Instance of a MySqlQuery implementation.
     */
    private $mySqlQuery;

    /**
     * MySqlUserCrudObserver constructor. Instantiates the MySqlQuery instance used to establish
     * a connection to the database password data is stored in.
     * @devNote: This class uses the CoreValues class to determine the name of the passwords database,
     *           and to get the required MySqlQuery instance. CoreValues is used to insure multiple
     *           instances of this class to not result in multiple database connections being opened
     *           to the same database.
     * @see MySqlQuery
     * @see CoreValues
     */
    public function __construct()
    {
        /**
         * Establish connection to passwords database.
         * @devNote: The connection to the password database is established via a MySqlQuery instance
         *           as opposed to a MySqlUserPasswordCrud instance because only the password id is
         *           updated by this observer, the rest of the password data MUST not be modified by
         *           instances of this class.
         */
        $this->mySqlQuery = CoreMySqlQuery::DbConnection(CoreValues::getPasswordsDBName());
    }


    /**
     * Receive update from an AMySqlUserCrud implementation instance.
     * @param SplSubject|AMySqlUserCrud $subject The AMySqlUserCrud implementation instance that issued the notice to update.
     *
     *                            WARNING: To conform the the SplObserver interface the
     *                            $subject parameter will accept any instance of an SplSubject,
     *                            however, this method will only perform as intended if an
     *                            of the AMySqlUserCrud implementation instance is passed to
     *                            the $subject parameter.
     * @return void
     */
    public function update(SplSubject $subject)
    {
        if (is_subclass_of($subject, 'DarlingCms\abstractions\crud\AMySqlUserCrud') && isset($subject->modType) && isset($subject->targetUser) && isset($subject->user)) {
            switch ($subject->modType) {
                case  AMySqlUserCrud::MOD_TYPE_UPDATE:
                    $sql = "UPDATE passwords SET passwordId = ? WHERE userName = ?";
                    if ($this->mySqlQuery->prepare($sql)->execute([$subject->user->getUserId(), $subject->targetUser->getUserName()]) === false) {
                        error_log('MySqlUserCrudObserver Error: Failed to update user ' . $subject->user->getUserName() . '\'s corresponding password data. WARNING: This may lock the user out of their account.' . ' | User Id: ' . $subject->user->getUserId());
                    }
                    break;
                case  AMySqlUserCrud::MOD_TYPE_DELETE:
                    //var_dump('MOD TYPE DELETE');
                    // @todo ! *ACTIVE* Implement this switch case... for user deletion...
                    break;
                default:
                    // Log error, invalid modification type
                    error_log('MySqlUserCrudObserver Error: Invalid modification type.');
                    break;
            }
        } else {
            error_log('MySqlUserCrudObserver Error: Failed to update user ' . $subject->user->getUserName() . '\'s corresponding password data. WARNING: This may lock the user out of their account.' . ' | User Id: ' . $subject->user->getUserId() . ' | Subject type was not valid.');
        }
    }


}
