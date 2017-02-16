<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2/8/17
 * Time: 2:37 AM
 */

namespace DarlingCms\classes\startup;


/**
 * Class darlingCmsStartup. Responsible for managing the startup of the Darling Cms, this includes
 * starting up apps and themes, and constructing and outputting the page.
 *
 * @package DarlingCms\classes\startup
 */
class darlingCmsStartup extends \DarlingCms\abstractions\startup\Astartup
{
    /**
     * @var \DarlingCms\classes\startup\appStartup $appStartup Instance of the \DarlingCms\classes\startup\appStartup() class.
     */
    private $appStartup;

    /**
     * @var \DarlingCms\classes\startup\themeStartup $themeStartup Instance of the \DarlingCms\classes\startup\themeStartup() class.
     */
    private $themeStartup;

    /**
     * @var array Array of startup objects. @todo : this is not used yet, but may be in the future.
     * If used it should replace the internal $startupObjects arrays in the start() and stop() methods.
     */
    private $startupObjects;

    /**
     * darlingCmsStartup constructor.
     * @param \DarlingCms\classes\startup\appStartup $appStartup Instance of the \DarlingCms\classes\startup\appStartup() class.
     * @param \DarlingCms\classes\startup\themeStartup $themeStartup Instance of the \DarlingCms\classes\startup\themeStartup() class.
     */
    public function __construct(\DarlingCms\classes\startup\appStartup $appStartup, \DarlingCms\classes\startup\themeStartup $themeStartup)
    {
        $this->appStartup = $appStartup;
        $this->themeStartup = $themeStartup;
        /**
         * The following code could possible be used to accommodate passing an indefinite number of startup objects to the __constructor()
         *
         * $this->startupObjects = func_get_args();
         * var_dump($this->startupObjects);
         *
         * ---- would make the following possible ... ----
         *
         * $dcms = new \DarlingCms\classes\startup\themeStartup($obj1, $obj, $obj3, ...);
         *
         * This change would require refactoring of the entire class...
         */
    }


    /**
     * @inheritDoc
     * Shutdown each startup object.
     *
     * @return bool Returns true if each startup object was shutdown without any errors, false otherwise.
     */
    protected function stop()
    {
        /* Initialize $stopStatus array which will track the success or failure of shutting down
           each startup object. */
        $stopStatus = array();
        /* Initialize array of startup objects. */
        $startupObjects = array($this->appStartup, $this->themeStartup);

        /* Shutdown each startup object. */
        foreach ($startupObjects as $startupObject) {
            // DEV CODE: Keep error reporting on while in dev. Once this class is complete, remove the following line:
            $startupObject->errorReporting(true);
            /* Store the result of calling the startup object's shutdown() method in the $stopStatus array. */
            array_push($stopStatus, $startupObject->shutdown());
        }
        /* Return true if there were no errors, i.e., false is not in $stopStatus array, false otherwise. */
        return !in_array(false, $stopStatus);
    }

    /**
     * @inheritDoc
     */
    protected function run()
    {
        /* Start the startup objects. */
        $this->start();

        /* Display the page. */
        $this->displayPage();
    }

    /**
     * Starts up the startup objects.
     *
     * @return bool True if all startup object's were startup successfully, false otherwise.
     */
    final private function start()
    {
        /* Initialize $startStatus array which will track the success or failure of starting up
           each startup object. */
        $startStatus = array();
        /* Initialize array of startup objects. */
        $startupObjects = array($this->appStartup, $this->themeStartup);

        /* Startup each startup object. */
        foreach ($startupObjects as $startupObject) {
            // DEV CODE: Keep error reporting on while in dev. Once this class is complete, remove the following line:
            $startupObject->errorReporting(true);
            /* Store the result of calling the startup object's startup() method in the $startStatus array. */
            array_push($startStatus, $startupObject->startup());
        }
        /* Return true if there were no errors, i.e., false is not in $startStatus array, false otherwise. */
        return !in_array(false, $startStatus);
    }

    /**
     *
     */
    final private function displayPage()
    {
        /* Display the html doctype. */
        $this->displayHtmlDoctype();

        echo PHP_EOL . '<html>' . PHP_EOL;

        $dcmsMsg = '<!-- This site is powered by the "Darling Cms" :) -->';
        echo PHP_EOL . $dcmsMsg . PHP_EOL;

        /* Display the html <head> */
        $this->displayHtmlHead();

        /* Display the html <body> */
        $this->displayHtmlBody();

        echo PHP_EOL . $dcmsMsg . PHP_EOL;

        echo PHP_EOL . '</html>' . PHP_EOL;

    }

    /**
     *
     */
    final private function displayHtmlDoctype()
    {
        echo '<!DOCTYPE html>' . PHP_EOL;
    }

    /**
     *
     */
    final private function displayHtmlHead()
    {
        echo PHP_EOL . '<head>' . PHP_EOL;

        echo PHP_EOL . '<title>' . (isset($_GET['page']) === true ? ucfirst(trim(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING))) : 'Darling Cms') . ' | ' . date('M d Y') . '</title>' . PHP_EOL;

        $this->themeStartup->displayThemeLinks();

        echo PHP_EOL . '</head>' . PHP_EOL;

    }

    /**
     *
     */
    final private function displayHtmlBody()
    {
        echo PHP_EOL . '<body>' . PHP_EOL;

        /* @todo: Need to find a way to govern what order app output is displayed...
         *        and where on the page...
         *        themeing issue?...
         *        user function?...
         *        Not sure how to implement....?
         */
        foreach ($this->appStartup->runningApps() as $runningApp) {
            $this->appStartup->displayAppOutput($runningApp);
        }

        echo PHP_EOL . '</body>' . PHP_EOL;

    }

}