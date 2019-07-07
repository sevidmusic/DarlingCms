<?php

/**
 * Created by Sevi Darling.
 * Date: 2019-02-20
 * Time: 17:38
 */

namespace DarlingCms\abstractions\crud;

use DarlingCms\classes\database\SQL\MySqlObjectQuery;
use DarlingCms\interfaces\crud\ISqlObjectQueryCrud;
use DarlingCms\interfaces\crud\ISqlQueryCrud;
use SplSubject;
use SplObserver;
use SplObjectStorage;

/**
 * Class AObservableMySqlObjectQueryCrud. Defines an abstract implementation
 * of the AMySqlObjectQueryCrud abstract class that implements the SplSubject,
 * ISqlQueryCrud, and ISqlObjectQueryCrud interfaces that can be used as a base
 * class for AMySqlObjectQueryCrud implementations that MUST be observable by
 * a SplObserver implementation instance.
 *
 * @package DarlingCms\abstractions\crud
 *
 * @see SplSubject
 * @see ISqlQueryCrud
 * @see ISqlObjectQueryCrud
 * @see AObservableMySqlObjectQueryCrud
 * @see AObservableMySqlObjectQueryCrud::attach()
 * @see AObservableMySqlObjectQueryCrud::detach()
 * @see AObservableMySqlObjectQueryCrud::notify()
 */
abstract class AObservableMySqlObjectQueryCrud extends AMySqlObjectQueryCrud implements SplSubject, ISqlQueryCrud, ISqlObjectQueryCrud
{
    /**
     * @var SplObjectStorage SplObjectStorage instance that is used to store
     *                       observers of this instance.
     */
    protected $observers;

    /**
     * AObservableMySqlObjectQueryCrud constructor. Injects the MySqlObjectQuery instance
     * that will handle CRUD operations, sets the name of the table CRUD operations
     * will be performed on, and attaches any specified SplObserver implementations
     * that are to observe this AObservableMySqlObjectQueryCrud implementation instance.
     *
     * @param MySqlObjectQuery $mySqlObjectQuery The MySqlObjectQuery instance that
     *                                           will handle CRUD operations.
     *
     * @param string $tableName The name of the table CRUD operations will be performed on.
     *
     * @param SplObserver ...$observers The observers of this AObservableMySqlObjectQueryCrud
     *                                  implementation instance.
     */
    public function __construct(MySqlObjectQuery $mySqlObjectQuery, string $tableName, SplObserver...$observers)
    {
        parent::__construct($mySqlObjectQuery, $tableName);
        $this->observers = new SplObjectStorage();
        foreach ($observers as $observer) {
            $this->observers->attach($observer);
        }
    }

    /**
     * Attach an SplObserver.
     *
     * @link https://php.net/manual/en/splsubject.attach.php
     *
     * @param SplObserver $observer The SplObserver to attach.
     *
     * @return void
     *
     * @since 5.1.0
     *
     */
    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }


    /**
     * Detach an observer
     *
     * @link https://php.net/manual/en/splsubject.detach.php
     *
     * @param SplObserver $observer SplObserver to detach.
     *
     * @return void
     *
     * @since 5.1.0
     *
     */
    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * Notify an observer.
     *
     * @link https://php.net/manual/en/splsubject.notify.php
     *
     * @return void
     *
     * @since 5.1.0
     *
     */
    public function notify(): void
    {
        /** @var SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
