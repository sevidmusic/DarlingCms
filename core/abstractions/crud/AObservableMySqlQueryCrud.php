<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2019-02-20
 * Time: 17:38
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\classes\database\SQL\MySqlQuery;
use SplSubject;
use SplObserver;
use SplObjectStorage;

/**
 * Class AObservableMySqlQueryCrud. Defines an abstract implementation of the AMySqlQueryCrud
 * abstract class that can be used as a base class for AMySqlQueryCrud implementations
 * that implement the SplSubject class, and are therefore observable by implementations of
 * the SplObserver class.
 * @package DarlingCms\abstractions\crud
 * @see AMySqlQueryCrud
 * @see SplSubject
 * @see SplObserver
 * @see SplObjectStorage
 * @see AObservableMySqlQueryCrud::attach()
 * @see AObservableMySqlQueryCrud::detach()
 * @see AObservableMySqlQueryCrud::notify()
 */
abstract class AObservableMySqlQueryCrud extends AMySqlQueryCrud implements SplSubject
{
    /**
     * @var SplObjectStorage SplObjectStorage instance that is used to store observers of this instance.
     */
    protected $observers;

    /**
     * AObservableMySqlQueryCrud constructor. Injects the MySqlQuery instance that will handle
     * CRUD operations, sets the name of the table CRUD operations will be performed on, and
     * attaches any specified SplObserver implementations that are to observe this
     * AObservableMySqlQueryCrud implementation instance.
     * @param MySqlQuery $MySqlQuery The MySqlQuery instance that will handle CRUD operations.
     * @param string $tableName The name of the table CRUD operations will be performed on.
     * @param SplObserver ...$observers The observers of this AObservableMySqlQueryCrud implementation instance.
     */
    public function __construct(MySqlQuery $MySqlQuery, string $tableName, SplObserver...$observers)
    {
        parent::__construct($MySqlQuery, $tableName);
        $this->observers = new SplObjectStorage();
        foreach ($observers as $observer) {
            $this->observers->attach($observer);
        }
    }

    /**
     * Attach an SplObserver
     * @link https://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }


    /**
     * Detach an observer
     * @link https://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * Notify an observer
     * @link https://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     * @devNote: observer/subject related logic
     */
    public function notify(): void
    {
        /** @var \SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
