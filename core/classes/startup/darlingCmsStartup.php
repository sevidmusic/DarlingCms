<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/24/17
 * Time: 1:30 AM
 */

namespace DarlingCms\classes\startup;


class darlingCmsStartup extends \DarlingCms\abstractions\startup\Astartup
{
    private $crud;
    private $enabledApps;

    /**
     *
     */
    public function __construct(\DarlingCms\abstractions\crud\AregisteredCrud $crud)
    {
        // public function __construct()
        /* Instantiate the crud. */
        $this->crud = $crud;
        $this->setEnabledApps();
    }

    private function setEnabledApps()
    {
        if (is_array($this->enabledApps) === false) {
            unset($this->enabledApps);
            $this->enabledApps = array();
        }
        /* Get the registry of stored data. */
        $registry = $this->crud->getRegistry();
        /* Loop through the registry and push any \DarlingCms\classes\component\app objects discovered
           into the $apps array. */
        foreach ($registry as $registryData) {
            /* If the data's classification is 'DarlingCms\classes\component\app'... */
            if ($registryData['classification'] === 'DarlingCms\classes\component\app') {
                /* ...read app object from storage and push it into the $apps array. */
                array_push($this->enabledApps, $this->crud->read($registryData['storageId']));
            }
        }
    }

    /**
     * Process any shutdown logic specific to the implementation.
     *
     * @return bool True if implementations specific shutdown logic
     *              was processed successfully, false otherwise.
     */
    protected function stop()
    {
        // TODO: Implement stop() method.
    }

    /**
     * Process any startup logic specific to the implementation.
     *
     * @return bool True if implementations specific startup logic
     *              was processed successfully, false otherwise.
     */
    protected function run()
    {
        // protected function run()
        /* Check if $apps array is empty. */
        switch (empty($this->enabledApps)) {
            case false:
                /* Startup each app. */
                foreach ($this->enabledApps as $app) {
                    if (is_object($app) && get_class($app) === 'DarlingCms\classes\component\app') {
                        /* Initialize an app startup object. */
                        $appStartup = new \DarlingCms\classes\startup\simpleAppStartup($app);
                        $appStartup->errorReporting(true);
                        /* Startup the app. */
                        $appStartup->startup();
                        /* Display the apps output. */
                        echo $appStartup->getAppOutput();
                        /* Display any errors that occurred. Errors only displayed if error reporting is turned on. */
                        $appStartup->displayErrors();
                    }

                }
                break;
            case true:
                /* Display message to user indicating that no apps are enabled, and a link offering to automatically enable any existing apps. */
                echo "<h3>There are no apps enabled! The Darling Cms relies on apps for it's functionality, no apps, no cms</h3>
              <p>Build and enable some apps and you'll see things start to happen...</p>
              <p><a href=\"index.php?initializeDarlingCms=true\">Click Here</a> if you would like the Darling Cms to attempt to enable any apps that exist for you this one time.</p>";
                /* If $_GET['initializeDarlingCms'] === true then attempt to enable existing apps via the app manager app. */
                if (boolval(filter_input(INPUT_GET, 'initializeDarlingCms')) === true) {
                    /* Create app component for the app manager app. */
                    $appManager = new \DarlingCms\classes\component\app('appManager');
                    /* Enable the app manager app. */
                    $appManager->enableApp();
                    /* Create a startup object to startup the app manager app. */
                    $appStartup = new \DarlingCms\classes\startup\simpleAppStartup($appManager);
                    /* Set error reporting to true while in dev. */
                    $appStartup->errorReporting(true);
                    /* Startup the app. */
                    $appStartup->startup();
                    /* Display the apps output. */
                    echo $appStartup->getAppOutput();
                    /* Display any errors that occurred. Errors only displayed if error reporting is turned on. */
                    $appStartup->displayErrors();
                }
                break;
        }
        return true;
    }
}
