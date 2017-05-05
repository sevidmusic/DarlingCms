<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Initialize the Darling Cms. */
$initializer = new \DarlingCms\classes\initializer\dcmsInitializer(new \DarlingCms\classes\crud\registeredJsonCrud());
$initializer->initialize();
$initialized = $initializer->getInitialized();
$appStartupObjects = $initialized['array']['appStartupObjects'];

if (empty($appStartupObjects) !== true) {
    /* Initialize multiAppStartup() object, passing the first singleAppStartup object to the constructor. */
    $mas = new  \DarlingCms\classes\startup\multiAppStartup(array_shift($appStartupObjects));


    /* Add the rest of the app startup objects to the multiAppStartup object. */
    foreach ($appStartupObjects as $appStartupObject) {
        $mas->setStartupObject($appStartupObject);
    }

    /* Startup the apps. */
    $mas->startup();
}
