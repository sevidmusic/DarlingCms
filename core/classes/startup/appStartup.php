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
    /* Error code aliases. */

    /**
     * @const int Error code for an error resulting from a failed attempt to include an app.
     */
    const INCLUDE_ERROR = 23458;

    /**
     * @const int Error code for an error resulting from an attempt to load an app that is disabled.
     */
    const APP_DISABLED_ERROR = 23458;

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
     * appStartup constructor. Initializes the enabledApps, runningApps, and appOutput arrays Also,
     * turns off error reporting, initially. Finally, protects against an infinite startup loop
     * that would occur if object is instantiated from within an app by disabling the instantiating
     * app if the instantiator is indeed an app. In other words, the __constructor() insures that it
     * is possible to instantiate an instance of the appStartup() object from within an app.
     */
    final public function __construct()
    {
        /* Determine what path and filename of file this object is being instantiated in. */
        $callerFile = debug_backtrace()[0]['file'];
        /* Filter $callerFile string to remove file path and .php extension. */
        $caller = str_replace('.php', '', substr($callerFile, strrpos($callerFile, "/") + 1));
        /* Determine enabled apps. */
        $this->enabledApps = $this->determineEnabledApps();
        /* Check if the $caller is one of the enabled apps. */
        if (in_array($caller, $this->enabledApps, true) === true) {
            /* If the $caller matches one of the enabled apps, disable the matching app to prevent causing an
               infinite startup loop when startup() is called for this instance. */
            $this->disableApp($caller);
        }
        /* Initialize the running apps array. */
        $this->runningApps = array();
        /* Initialize app output array. */
        $this->appOutput = array();
        /* Turn error reporting off, initially. */
        $this->errorReporting(false);
    }

    /**
     * Determines which apps are enabled and returns their names in an array.
     *
     * @todo: This method should take dependency into account, it currently does not!
     *
     * @return array Array of enabled apps.
     */
    final private function determineEnabledApps()
    {
        return array('helloWorld', 'helloUniverse');
    }

    /**
     * Disable an app.
     *
     * Note: This only disables the app for the instance that called this method.
     *
     * @todo: This method should take dependency into account, it currently does not!
     *
     * @param string $app Name of the app to disable.
     *
     * @return bool True if app was disabled successfully, false otherwise.
     *              If app was not enabled initially, this method will still return true.
     */
    final public function disableApp(string $app)
    {
        if (($key = array_search($app, $this->enabledApps)) !== false) {
            unset($this->enabledApps[$key]);
        }
        return !isset($this->enabledApps[$key]);
    }

    /**
     * Enable an app.
     *
     * Note: This only enables the app for the instance that called this method.
     *
     * @todo: This method should take dependency into account, it currently does not!
     *
     * @param string $app Name of the app to enable.
     *
     * @return bool True if app was enabled successfully, false otherwise.
     *              If app was enabled initially, this method will still return true.
     */
    final public function enableApp(string $app)
    {
        if (($key = array_search($app, $this->enabledApps)) === false) {
            array_push($this->enabledApps, $app);
        }
        return isset($this->enabledApps[$key]);
    }

    /**
     * Check if an app is enabled.
     *
     * Note: This only checks if the app is enabled for the instance that called this method.
     *
     * @param string $app Name of the app to enable.
     *
     * @return bool True if app is enabled, false otherwise.
     *
     */
    final public function isEnabled(string $app)
    {
        /* Search for the $app in the enabledApps array. */
        return (array_search($app, $this->enabledApps) !== false);
    }

    /**
     * Returns the enabledApps array in it's current state.
     *
     * @return array The enabledApps array in it's current state.
     */
    final public function enabledApps()
    {
        return $this->enabledApps;
    }

    /**
     * Echos the specified app's output and then returns it as a string.
     *
     * @param string $app The name of the app whose output should be displayed.
     *
     * @return string|bool The app's output as a string, or false on failure.
     *
     */
    public function displayAppOutput(string $app)
    {
        echo PHP_EOL . "<!-- $app App Output -->";
        echo PHP_EOL . $this->getAppOutput($app) . PHP_EOL;
        echo "<!-- End $app App Output -->" . PHP_EOL;
        return $this->getAppOutput($app);
    }

    /**
     * Returns the output of the specified app as a string.
     *
     * @param string $app The name of the app whose output should be returned.
     *
     * @return string|bool The app's output as a string, or false on failure.
     */
    final public function getAppOutput(string $app)
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
    final public function runningApps()
    {
        return $this->runningApps;
    }

    /**
     * Resets the appOutput, and runningApps arrays. Returns true if arrays were reset
     * successfully, false otherwise.
     *
     * @return bool True if arrays were reset successfully, false otherwise
     */
    final protected function stop()
    {
        /* Unset appOutput and runningApps arrays. */
        unset($this->appOutput);
        unset($this->runningApps);
        /* Re-initialize appOutput and runningApps arrays. */
        $this->appOutput = array();
        $this->runningApps = array();
        /* Check that appOutput and runningApps arrays were properly reset. */
        if (is_array($this->appOutput) === true && is_array($this->runningApps) === true && empty($this->appOutput) === true && empty($this->runningApps) === true) {
            /* Return true if appOutput and runningApps arrays were reset. */
            return true;
        }
        /* Return false if appOutput and runningApps arrays were not reset. */
        return false;
    }

    /**
     *
     * Loads enabled apps and handles error reporting.
     *
     * @return bool True if all enabled apps were loaded successfully, false otherwise.
     */
    final protected function run()
    {
        /* Load each enabled app. */
        foreach ($this->enabledApps as $enabledApp) {
            $this->loadApp($enabledApp);
        }
        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();
        /* Return true if there were no errors, i.e., the errors array is empty, false otherwise. */
        return empty($this->getErrors());
    }

    /**
     * Load's the requested $enabledApp.
     *
     * @param string $enabledApp The name of the enabled app to load.
     *
     * @return bool True if app was loaded successfully, false otherwise.
     */
    final private function loadApp(string $enabledApp)
    {
        if (in_array($enabledApp, $this->enabledApps) === false) {
            /* Register an error if an attempt is made to load an app that is not enabled. */
            $this->registerInternalError($enabledApp, self::APP_DISABLED_ERROR);
            /* Return false if requested $enabledApp is not actually enabled. */
            return false;
        }
        /* Count initial errors in order to differentiate between previous and and new errors. */
        $initialErrorCount = count($this->getErrors());
        /* Capture app output. */
        $this->captureAppOutput($enabledApp);
        /* Do a final error count. */
        $finalErrorCount = count($this->getErrors());
        /* Check if there were any errors by comparing the initial and final error counts. */
        return ($initialErrorCount === $finalErrorCount);
    }

    /**
     * Constructs the appropriate error message for the provided $errorCode and uses the
     * registerError() method to register the error.
     *
     * This method will return false if the $errorCode is invalid, or if the error failed
     * to be registered.
     *
     * @param string $appName Name of the app that caused the error.
     *
     * @param int $errorCode Error code for this error. (options: INCLUDE_ERROR, APP_DISABLED_ERROR)
     *
     * @param null $data (optional) Any data associated with the error.
     *
     * @return bool True if error was registered successfully, false otherwise.
     */
    final private function registerInternalError(string $appName, int $errorCode, $data = null)
    {
        $enabledApp = $appName;
        switch ($errorCode) {
            case 23458:
                $index = '<div class=\'dcmsErrorContainerTitle\'>Startup Error for app "' . $enabledApp . '"</div>';
                $message = "
                    <div class='dcmsErrorContainer'>
                      <p>An error occurred while attempting to startup the \"$enabledApp\" app.</p>
                      <p>Please check the following:</p>
                      <ul>
                        <li>Is the \"$enabledApp\" app installed?</li>
                        <li>Does the \"$enabledApp\" app's directory name match \"$enabledApp\"?</li>
                        <li>Does the \"$enabledApp\" app's php file name match \"$enabledApp . php\"?</li>
                      </ul>
                    </div>
                ";
                break;
            case 23475:
                $index = '<div class="dcmsErrorContainerTitle">Startup Error for app "' . $enabledApp . '"</div>';
                $message = '<div class="dcmsErrorContainer">Attempt made to load an app that is not enabled!</div>';
                break;
            default:
                /* Return false if $errorCode does not exist. */
                return false;
        }

        return $this->registerError($index, $message, $data);

    }

    /**
     * Uses an output buffer to capture output from a specified app and stores it
     * in the appOutput array.
     *
     * @param string $enabledApp Name of the app to capture output from.
     *
     * @return bool True if output was captured in the appOutput array successfully, false otherwise.
     */
    final  private function captureAppOutput(string $enabledApp)
    {
        /* Start output buffer. */
        ob_start();
        /* Attempt to include the app. */
        if ($this->includeApp($enabledApp) === false) {
            /* If include failed, register an error. */
            $this->registerInternalError($enabledApp, self::INCLUDE_ERROR);
        } else {
            /* If include succeeded add app to the running apps array. */
            array_push($this->runningApps, $enabledApp);
            /* Capture app output from the buffer and add it to the $appOutput array. */
            $this->appOutput[$enabledApp] = ob_get_contents();
        }
        /* End output buffer */
        ob_end_clean();
        /* Return true if app output was captured, false otherwise. */
        return isset($this->appOutput[$enabledApp]);
    }

    /**
     * @param string $enabledApp Name of the app to include.
     * @return mixed Returns the int 1 if include succeeded, false otherwise.
     */
    final private function includeApp(string $enabledApp)
    {
        return include(str_replace('core/classes/startup', '', __DIR__) . 'apps/' . $enabledApp . '/' . $enabledApp . '.php');
    }

    /**
     * Custom implementation of displayErrors() for appStartup() class. This implementations
     * captures the result of calling displayErrors() in a buffer and adds the output to the
     * app output array under the index "dcmsAppStartupErrors".
     *
     * @param bool $forceDisplay If set to true, then displayErrors() will function the same
     * as the parent implementation's displayErrors() echoing the errors to the page immediately.
     * If set to false then the custom implementation will be used, i.e., an output buffer is used to
     * capture the output of the parent's displayErrors() and the output is added to the app output
     * array. (Defaults to false)
     *
     * @return bool Returns the value of $forceDisplay.
     */
    final public function displayErrors(bool $forceDisplay = false)
    {
        if ($forceDisplay === false) {
            /* Name of internal app used to output the errors display. */
            $internalErrorAppId = 'dcmsAppStartupErrors';
            /* Use a buffer to capture error display. */
            ob_start();
            /* Call parent's displayErrors(). */
            parent::displayErrors();
            /* Capture output and store it in appOutput array. */
            $this->appOutput[$internalErrorAppId] = ob_get_contents();
            /* End and cleanup buffer used to capture error display output. */
            ob_end_clean();
            /* If there were any errors... */
            if (empty($this->getErrors()) === false) {
                /* Register the error output as a running app since it now exists as part of the app output array. */
                array_push($this->runningApps, $internalErrorAppId);
            }
            return $forceDisplay;
        }
        parent::displayErrors();
        return $forceDisplay;
    }

}
