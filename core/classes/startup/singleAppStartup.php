<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/23/17
 * Time: 11:21 PM
 */

namespace DarlingCms\classes\startup;

/**
 * Class singleAppStartup. Starts up a single \DarlingCms\classes\component\app component.
 *
 * @todo: Consider adding a getOutput() method to the contract of a startup object
 *        so startup objects have the option of capturing and managing output on startup,
 *        restart, or shutdown. This new method should be added to Astartup or Istartup.
 *
 * @package DarlingCms\classes\startup
 */
class singleAppStartup extends \DarlingCms\abstractions\startup\Astartup
{
    private $app;
    private $appDirectoryPath;
    private $appFileName;
    private $appFileExtension;
    private $appFilePath;

    /**
     * Returns the internal \DarlingCms\classes\component\app object instance.
     * @return \DarlingCms\classes\component\app The current \DarlingCms\classes\component\app app instance.
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set the internal \DarlingCms\classes\component\app object instance.
     * @param \DarlingCms\classes\component\app $app Instance of a \DarlingCms\classes\component\app object.
     * @return bool True if app was set, false otherwise.
     */
    public function setApp(\DarlingCms\classes\component\app $app)
    {
        $this->__construct($app);
        return isset($app);
    }

    /**
     * Initialize the $app, $appDirectoryPath, $appFileName, $appFileExtension, and $appFilePath properties.
     * Note: The __construct() method is called upon instantiation and whenever setApp() is called.
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
     * Captures the app's output, if the app is enabled. The captured app
     * output is stored as a component attribute in the custom attributes array
     * under the index 'appOutput'.
     *
     * Hint: To get the app output, use the internal \DarlingCms\classes\component\app object
     * instance's getComponentAttributeValue() method.
     *
     * i.e., $this->getApp()->getComponentAttributeValue('customAttributes')['appOutput'];
     *
     * @return bool True if app output was captured, false otherwise.
     */
    protected function run()
    {
        /* Only run if app is enabled.  */
        if ($this->app->getComponentAttributeValue('enabled') === true) {
            $status = $this->captureAppOutput();
            /* For now...display output. */
            echo $this->app->getComponentAttributeValue('customAttributes')['appOutput'];
            return $status;
        }
        return false;
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
        /* Clear app output from any previous start-ups. */
        $this->stop();
        /* Start output buffer. */
        ob_start();
        /* Attempt to include the app. */
        if ($this->includeApp() === false) {
            /* End output buffer */
            ob_end_clean();
            /* Include failed, return false. */
            return false;
        }
        /* Capture app output from the output buffer. */
        $this->app->setCustomAttribute('appOutput', trim(ob_get_contents()));
        /* End output buffer */
        ob_end_clean();
        /* Return true if app output was captured, false otherwise. */
        return (is_null($this->app->getComponentAttributeValue('customAttributes')['appOutput']) === false);
    }

    /**
     * Clears the app output by setting it to null.
     *
     * @return bool True if app output was cleared, false otherwise.
     */
    protected function stop()
    {
        return $this->app->setCustomAttribute('appOutput', null);
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
