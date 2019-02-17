<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-08
 * Time: 01:43
 */

namespace DarlingCms\classes\observer\crud;


use DarlingCms\classes\crud\MySqlUserPasswordCrud;
use DarlingCms\classes\staticClasses\core\CoreMySqlQuery;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\user\UserPassword;
use SplSubject;
use DarlingCms\abstractions\crud\AMySqlUserCrud;

class MySqlUserCrudObserver implements \SplObserver
{

    private $mySqlQuery;

    /**
     * MySqlUserCrudObserver constructor.
     * @param MySqlUserPasswordCrud $passwordCrud
     */
    public function __construct()
    {
        $this->mySqlQuery = CoreMySqlQuery::DbConnection(CoreValues::USERS_DB_NAME);
    }


    /**
     * Receive update from subject
     * @link https://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     * The <b>SplSubject</b> notifying the observer of an update.
     * </p>
     * @return void
     * @since 5.1.0
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
        }
    }


}
