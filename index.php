<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Instantiate the crud. */
$crud = new \DarlingCms\classes\crud\registeredJsonCrud();
/* Get the registry of stored data. */
$registry = $crud->getRegistry();
/* Initialize $apps array. This array will hold all the loaded app objects. */
$apps = array();
/* Loop through the registry and push any app objects into the $apps array. */
foreach ($registry as $registryData) {
    /* If the data's classification is 'DarlingCms\classes\component\app'... */
    if ($registryData['classification'] === 'DarlingCms\classes\component\app') {
        /* ...read app object from storage and push it into the $apps array. */
        array_push($apps, $crud->read($registryData['storageId']));
    }
}

/* Check if $apps array is empty. */
switch (empty($apps)) {
    case false:
        /* Startup each app. */
        foreach ($apps as $app) {
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





