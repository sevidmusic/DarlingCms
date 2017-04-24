<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/23/17
 * Time: 11:21 PM
 */

namespace DarlingCms\classes\startup;


class simpleAppStartup extends \DarlingCms\abstractions\startup\Astartup
{
    private $app;
    private $appOutput;
    private $appDirectoryPath;
    private $appFileName;
    private $appFileExtension;
    private $appFilePath;

    /**
     * Initialize the $app, $appDirectoryPath, $appFileName, $appFileExtension, and $appFilePath properties.
     */
    public function __construct(\DarlingCms\classes\component\app $app)
    {
        $this->app = $app;
        $this->appDirectoryPath = str_replace('core/classes/startup', '', __DIR__) . 'apps/' . $app->getComponentName() . '/';
        $this->appFileName = $app->getComponentName();
        $this->appFileExtension = 'php';
        $this->appFilePath = "$this->appDirectoryPath$this->appFileName.$this->appFileExtension";
    }

    /**
     * Returns the string of app output captured by the captureAppOutput() method.
     * @return string The app's output as a string.
     */
    final public function getAppOutput()
    {
        return $this->appOutput;
    }

    /**
     * Process any shutdown logic specific to the implementation.
     *
     * @return bool True if implementations specific shutdown logic
     *              was processed successfully, false otherwise.
     */
    protected function stop()
    {
        return true;
    }

    /**
     * Process any startup logic specific to the implementation.
     *
     * @return bool True if implementations specific startup logic
     *              was processed successfully, false otherwise.
     */
    protected function run()
    {
        $this->captureAppOutput();
        return true;
    }

    /**
     * Uses an output buffer to capture output from a specified app and stores it
     * in the appOutput array indexed under the name of the specified app.
     *
     * @param string $enabledApp Name of the app to capture output from.
     *
     * @return bool True if output was captured in the appOutput array successfully, false otherwise.
     */
    final  private function captureAppOutput()
    {
        /* Start output buffer. */
        ob_start();
        /* Attempt to include the app. */
        if ($this->includeApp() === false) {
            /* End output buffer */
            ob_end_clean();
            /* Include failed, return false. */
            return false;
        }
        /* Capture app output from the buffer and add it to the $appOutput array. */
        $this->appOutput = ob_get_contents();
        /* End output buffer */
        ob_end_clean();
        /* Return true if app output was captured, false otherwise. */
        return isset($this->appOutput);
    }

    /**
     * Attempts to include the specified app.
     *
     * @param string $enabledApp Name of the app to include.
     * @return mixed Returns the int 1 if include succeeded, false otherwise.
     */
    final private function includeApp()
    {
        return include($this->appFilePath);
    }

}
