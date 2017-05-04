<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/27/17
 * Time: 8:40 AM
 */

namespace DarlingCms\classes\startup;


class multiAppStartup extends \DarlingCms\classes\startup\multiStartup
{
    private $runningApps;
    private $singleAppStartup;
    private $app;

    /**
     * Adds a startup object to the internal $startupObjects array.
     *
     * @param \DarlingCms\interfaces\startup\Istartup $startupObject The startup object to add.
     *
     * Note: This implementation further requires that the startup object implement the
     * \DarlingCms\classes\startup\singleAppStartup implementation of the
     * \DarlingCms\interfaces\startup\Istartup interface, and will return false if the
     * startup object is not in fact a instance of the singleAppStartup() class.
     *
     * @return bool True if startup object was added to the startup objects array, false otherwise.
     */
    public function setStartupObject(\DarlingCms\interfaces\startup\Istartup $startupObject)
    {
        /* Initialize the running apps array. */
        $this->runningApps = array();
        /* Ensure that the startup object is specifically an instance of the singleAppStartup() implementation
           of the Istartup interface. */
        if (get_class($startupObject) === 'DarlingCms\classes\startup\singleAppStartup') {
            /* If startup object is in fact an instance of the singleAppStartup() class. */
            return parent::setStartupObject($startupObject);
        }
        /* Return false if $startupObject is not an instance of the singleAppStartup() class. */
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
     * Calls the startup() method of each startup object.
     *
     * @return bool True if each startup object started up successfully, false otherwise.
     */
    protected function run()
    {
        /* Initialize status array. Tracks success or failure of each call to startup(). */
        $status = array();
        foreach ($this->startupObjects as $startupObject) {
            $this->setSingleAppStartup($startupObject);
            $this->setApp($this->singleAppStartup->getApp());
            /* Start the app. */
            array_push($status, $this->startApp());
        }

        /* Display app output. */
        foreach ($this->runningApps as $app) {
            $this->setApp($app);
            echo $this->app->getComponentAttributeValue('customAttributes')['appOutput'] . PHP_EOL;
        }
        /* Return true if all calls to setStartupObject() returned true, false otherwise. */
        return (in_array(false, $status) === false);
    }

    private function setSingleAppStartup(\DarlingCms\classes\startup\singleAppStartup $singleAppStartup)
    {
        $this->singleAppStartup = $singleAppStartup;
        return isset($this->singleAppStartup);
    }

    private function setApp(\DarlingCms\classes\component\app $app)
    {
        $this->app = $app;
        return isset($this->app);
    }

    private function startApp()
    {
        /* Make sure app is not already running. */
        if (in_array($this->app->getComponentName(), array_keys($this->runningApps, true)) === true) {
            /* Return false if app is already running. */
            return false;
        }
        /* Make sure dependencies have been met, and app is not already running. */
        if ($this->dependenciesMet() === true) {
            $status = array();
            /* "Tell" the app about the app components that are already running. */
            $this->app->setCustomAttribute('runningApps', $this->runningApps);
            /* Call startup() and store result in $status array. */
            array_push($status, $this->singleAppStartup->startup());
            /* Sync internal running apps array with components modified running apps array. */
            unset($this->runningApps);
            $this->runningApps = $this->app->getComponentAttributeValue('customAttributes')['runningApps'];
            /* Unset the app's running apps array, no need to keep this data after the app has been processed. */
            $this->app->setCustomAttribute('runningApps', array());
            /* Add the app to the internal running apps array. */
            $this->runningApps[$this->app->getComponentName()] = $this->app;
            return (in_array(false, $status) === false);
        }
        /* Return false if all dependencies were not met. */
        return false;
    }

    private function dependenciesMet()
    {
        /* Get the app's dependencies. */
        $dependencies = $this->app->getComponentAttributeValue('dependencies');
        /* Store so and app being processed for future reference */
        $processingSo = $this->singleAppStartup;
        $processingApp = $this->app;

        /* Cycle through dependencies. */
        foreach ($dependencies as $dependency) {
            /* If dependency is not already running. */
            if (in_array($dependency, array_keys($this->runningApps)) === false) {
                /* Get dependency's startup object. */
                $this->setSingleAppStartup($this->getSingleAppStartupObject($dependency));
                $this->setApp($this->singleAppStartup->getApp());
                /* Startup the dependency. */ //var_dump("Starting up $dependency, it is required by $appName");
                $this->startApp();
                $this->setSingleAppStartup($processingSo);
                $this->setApp($processingApp);
            }
        }
        return true;
    }

    private function getSingleAppStartupObject(string $appName)
    {
        foreach ($this->startupObjects as $startupObject) {
            if ($startupObject->getApp()->getComponentName() === $appName) {
                return $startupObject;
            }
        }
        return false;
    }
}
