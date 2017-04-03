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
     * Index of app startup object in startupObjects array.
     */
    const APP_STARTUP_OBJECT_INDEX = 0;

    /**
     * Index of theme startup object in startupObjects array.
     */
    const THEME_STARTUP_OBJECT_INDEX = 1;

    /**
     * Name of the interface each startup object must implement to be considered a valid startup object.
     */
    const DCMS_STARTUP_INTERFACE = 'DarlingCms\interfaces\startup\Istartup';
    /**
     * @var array Array of startup objects.
     */
    private $startupObjects;

    /**
     * darlingCmsStartup constructor. Initializes $startupObjects array, and insures that all startup objects
     * passed to the constructor implement the \DarlingCms\interfaces\startup\Istartup interface. The first
     * two startup objects must implement the \DarlingCms\classes\startup\appStartup and
     * \DarlingCms\classes\startup\themeStartup classes respectively.
     * @param \DarlingCms\classes\startup\appStartup $appStartup Instance of the \DarlingCms\classes\startup\appStartup() class.
     * @param \DarlingCms\classes\startup\themeStartup $themeStartup Instance of the \DarlingCms\classes\startup\themeStartup() class.
     * @param \DarlingCms\interfaces\startup\Istartup $startupObjects,... Additional startup objects.
     */
    public function __construct(\DarlingCms\classes\startup\appStartup $appStartup, \DarlingCms\classes\startup\themeStartup $themeStartup, \DarlingCms\interfaces\startup\Istartup $startupObjects = null)
    {
        $this->startupObjects = array();
        $parameters = func_get_args();
        foreach ($parameters as $startupObject) {
            $implements = class_implements($startupObject);
            if (in_array($this::DCMS_STARTUP_INTERFACE, $implements, true) === true) {
                array_push($this->startupObjects, $startupObject);
            }
        }
    }

    /**
     * @inheritDoc
     * Responsible for turning error reporting on or off for each startup object,
     * and for this darlingCmsStarup() instance.
     */
    public function errorReporting(bool $value)
    {
        /* Set each startup object's error reporting to the specified $value. */
        foreach ($this->startupObjects as $startupObject) {
            $startupObject->errorReporting($value);
        }
        /* Call parent's errorReporting() method to turn error reporting on for this darlingCmsStartup() instance. */
        return parent::errorReporting($value);
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

        /* Shutdown each startup object. */
        foreach ($this->startupObjects as $startupObject) {
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

        /* Startup each startup object. */
        foreach ($this->startupObjects as $startupObject) {
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
     * Display the html <head> tag's content.
     */
    final private function displayHtmlHead()
    {
        echo PHP_EOL . '<head>' . PHP_EOL;
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
        echo PHP_EOL . '<title>' . ($page !== false ? ucfirst(trim($page)) : 'Darling Cms') . ' | ' . date('M d Y') . '</title>' . PHP_EOL;
        $this->startupObjects[$this::THEME_STARTUP_OBJECT_INDEX]->displayThemeLinks();
        echo PHP_EOL . '</head>' . PHP_EOL;
    }

    /**
     *
     */
    final private function displayHtmlBody()
    {
        echo PHP_EOL . '<body>' . PHP_EOL;
        /* Get display each running apps output. */
        foreach ($this->startupObjects[$this::APP_STARTUP_OBJECT_INDEX]->runningApps() as $runningApp) {
            $this->startupObjects[$this::APP_STARTUP_OBJECT_INDEX]->displayAppOutput($runningApp);
        }

        echo PHP_EOL . '</body>' . PHP_EOL;

    }

}
