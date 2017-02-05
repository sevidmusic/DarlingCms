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
        $this->enabledApps = array('helloWorld', 'helloUniverse');
        /* Turn error reporting off initially. */
        $this->errorReporting(false);
        /* Initialize the running apps array. */
        $this->runningApps = array();
        /* Initialize app output array. */
        $this->appOutput = array();
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
            /* Start output buffer. */
            ob_start();
            /* Attempt to include the app. */
            if ((include str_replace('core/classes/startup', '', __DIR__) . 'apps/' . $enabledApp . '/' . $enabledApp . '.php') === false) {
                /* If include failed, register an error. */
                $this->registerError('Startup Error for app "' . $enabledApp . '"', "
                    <div style='border: 3px solid; padding: 9px;'>
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
            ob_end_flush();
        }

        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();

        /* Return true if there were no errors, i.e., the errors array is empty, false otherwise. */
        return empty($this->getErrors());
    }
}
