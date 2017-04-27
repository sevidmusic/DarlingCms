<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/27/17
 * Time: 8:40 AM
 */

namespace DarlingCms\classes\startup;


class multiAppStartup extends \DarlingCms\classes\startup\darlingCmsStartup
{
    /**
     * Adds a startup object to the internal $startupObjects array.
     *
     * @param \DarlingCms\interfaces\startup\Istartup $startupObject The startup object to add.
     *
     * @return bool True if startup object was added to the startup objects array, false otherwise.
     */
    public function setStartupObject(\DarlingCms\interfaces\startup\Istartup $startupObject)
    {
        if (get_class($startupObject) === 'DarlingCms\classes\startup\singleAppStartup') {
            return parent::setStartupObject($startupObject);
        }
        return false;
    }

    /**
     * Class the shutdown() method of each startup object.
     *
     * @return bool True if each startup object shutdown successfully, false otherwise.
     */
    protected function stop()
    {
        return parent::stop();
    }

    /**
     * Class the startup() method of each startup object.
     *
     * @return bool True if each startup object started up successfully, false otherwise.
     */
    protected function run()
    {
        return parent::run();
    }

}
