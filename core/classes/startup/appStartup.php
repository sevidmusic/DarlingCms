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
     * @var bool $errorReporting Boolean representing whether or not error reporting is on or off, true
     *                           or false respectively.
     */
    private $errorReporting;

    /**
     * @var array Array of running apps indexed in order of startup.
     */
    private $runningApps;

    /**
     * appStartup constructor. Initializes the $enabledApps and $runningApps arrays. Also,
     * turns off error reporting, initially.
     */
    public function __construct()
    {
        /* Initialize enabled apps array. */
        $this->enabledApps = array('helloWorld', 'helloUniverse', 'doesNotExist');
        /* Turn error reporting off initially. */
        $this->errorReporting(false);
        /* Initialize the running apps array. */
        $this->runningApps = array();
    }

    /**
     * Turns error reporting on or off.
     *
     * @param bool $value True to switch error reporting on, false to turn error reporting off.
     *
     * @return bool Returns true if error reporting is on, false otherwise.
     */
    public function errorReporting(bool $value)
    {
        /* Set error reporting to the specified $value. */
        $this->errorReporting = $value;
        /* Return error reporting state. */
        return $this->errorReporting;
    }

    /**
     * @inheritDoc
     */
    protected function stop()
    {
        /* No shutdown process yet, register error. */
        $this->registerError('Shutdown Error', '
                   <p>At the moment the Darling Cms does not allow apps to be shutdown after
                   they have been started up.</p>
               ');
        /* Return false till shutdown logic is implemented. */
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
            /* Attempt to include each enabled app. */
            if ((include str_replace('core/classes/startup', '', __DIR__) . 'apps/' . $enabledApp . '/' . $enabledApp . '.php') === false) {
                /* If include failed, register an error. */
                $this->registerError('Startup Error for app "' . $enabledApp . '"', "
                    <div style='border: 3px solid; padding: 9px;'>
                      <p>An error occurred while attempting to startup the \"$enabledApp\" app.</p>
                      <p>Please check the following:</p>
                      <ul>
                        <li>Is the \"$enabledApp\" installed?</li>
                        <li>Does the \"$enabledApp\" app's directory name match \"$enabledApp\"?</li>
                        <li>Does the \"$enabledApp\" app's php file name match \"$enabledApp.php\"?</li>
                      </ul>
                    </div>
                ");
            } else {
                /* If include succeeded add it to the running apps array. */
                array_push($this->runningApps, $enabledApp);
            }
        }
        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();

        /* Return true if there were no errors, i.e., the errors array is empty, false otherwise. */
        return empty($this->getErrors());
    }

    /**
     * If error reporting is turned on, display any errors that occurred during last call to
     * startup(), shutdown(), or restart().
     */
    public function displayErrors()
    {
        /* Check if error reporting is on. */
        if ($this->errorReporting === true) {
            /* Loop through and display each error. */
            foreach ($this->getErrors() as $index => $error) {
                /**
                 * If $error is an array, the error message and error data should extracted for display,
                 * otherwise, just display the error message.
                 */
                switch (is_array($error)) {
                    case true:
                        /* Extract and display error message from $error array. Use $index to indicate
                           which app caused the error. */
                        echo "<p>$index: {$error['message']}</p>";
                        /* var_dump() the error data from the $error array. */
                        var_dump($error['data']);
                        break;
                    case false:
                        /* $error is the error message, so, display $error. Use $index to indicate which
                           app caused the error. */
                        echo "<p>$index: $error</p>";
                        break;
                }
            }
        }
    }
}
