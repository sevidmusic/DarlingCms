<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2/2/17
 * Time: 11:16 PM
 */

namespace DarlingCms\classes\startup;

/**
 * Class appStartup. Responsible for managing the startup of enabled apps.
 *
 * @package DarlingCms\classes\startup
 */
class appStartup extends \DarlingCms\abstractions\startup\Astartup
{
    /**
     * @var array Array of enabled apps.
     */
    private $enabledApps;

    /**
     * @var array Array of running apps indexed in order of startup.
     */
    private $runningApps;

    /**
     * @var array Array of app output indexed by app name.
     */
    private $appOutput;

    /**
     * appStartup constructor. Initializes the $enabledApps and $runningApps arrays. Also,
     * turns off error reporting, initially.
     */
    public function __construct()
    {
        /* Initialize enabled apps array. */
        $this->enabledApps = array('helloWorld', 'helloUniverse', 'duh');
        /* Turn error reporting off initially. */
        $this->errorReporting(false);
        /* Initialize the running apps array. */
        $this->runningApps = array();
        /* Initialize app output array. */
        $this->appOutput = array();
    }

    /**
     * Echos the specified app's output to the page. This method has
     * no return value.
     *
     * @param string $app The app whose output should be displayed.
     *
     */
    public function displayAppOutput(string $app)
    {
        echo PHP_EOL . "<!-- $app App Output -->";
        echo PHP_EOL . $this->getAppOutput($app) . PHP_EOL;
        echo "<!-- End $app App Output -->" . PHP_EOL;
    }

    /**
     * Returns the output of the specified app as a string.
     *
     * @param string $app The name of the app whose output should be returned.
     *
     * @return string|bool The app's output as a string, or false on failure.
     */
    public function getAppOutput(string $app)
    {
        if (isset($this->appOutput[$app]) === true) {
            return $this->appOutput[$app];

        }
        return false;
    }

    /**
     * Returns an array of running apps, i.e., apps that started up successfully that are still running.
     *
     * @return array Array of running apps.
     */
    public function runningApps()
    {
        return $this->runningApps;
    }

    /**
     * @inheritDoc
     */
    protected function stop()
    {
        unset($this->appOutput);
        unset($this->runningApps);
        $this->appOutput = array();
        $this->runningApps = array();
        if (is_array($this->appOutput) === true && is_array($this->runningApps) === true && empty($this->appOutput) && empty($this->runningApps)) {
            return true;
        }
        /* Return false if appOutput and runningApps arrays were not reset. */
        return false;
    }

    /**
     * @inheritDoc
     * Includes any enabled apps.
     *
     * @return bool True if all enabled apps were included successfully, i.e. without errors, false otherwise.
     */
    protected function run()
    {
        /* Loop through enabled apps array. */
        foreach ($this->enabledApps as $enabledApp) {
            /* Start output buffer. */
            ob_start();
            /* Attempt to include the app. */
            if ((include str_replace('core/classes/startup', '', __DIR__) . 'apps/' . $enabledApp . '/' . $enabledApp . '.php') === false) {
                /* If include failed, register an error. */
                $this->registerError('Startup Error for app "' . $enabledApp . '"', "
                    <div class='dcmsErrorContainer'>
                      <p>An error occurred while attempting to startup the \"$enabledApp\" app.</p>
                      <p>Please check the following:</p>
                      <ul>
                        <li>Is the \"$enabledApp\" app installed?</li>
                        <li>Does the \"$enabledApp\" app's directory name match \"$enabledApp\"?</li>
                        <li>Does the \"$enabledApp\" app's php file name match \"$enabledApp.php\"?</li>
                      </ul>
                    </div>
                ");
            } else {
                /* If include succeeded add app to the running apps array. */
                array_push($this->runningApps, $enabledApp);
                /* Capture app output from the buffer and add it to the $appOutput array. */
                $this->appOutput[$enabledApp] = ob_get_contents();
            }
            /* End output buffer */
            ob_end_clean();
        }

        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();

        /* Return true if there were no errors, i.e., the errors array is empty, false otherwise. */
        return empty($this->getErrors());
    }
}
