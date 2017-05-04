<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Initialize crud object. */
$crud = new \DarlingCms\classes\crud\registeredJsonCrud();

/* Initialize appStartupObjects array. */
$appStartupObjects = array();

/* Search registry for any data classified as an app component. */
foreach ($crud->getRegistry() as $storageId => $registryData) {
    /* Check if the data's classification indicates an app component. */
    if ($crud->getRegistryData($storageId, 'classification') === 'DarlingCms\classes\component\app') {
        /* Load the app component. */
        if (($app = $crud->read($storageId)) !== false) {
            /* Create a singleAppStartup instance for the app component and add it to the $appStartupObjects array. */
            array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($app));
        }
    }
}

/* Determine if this is a fresh install of the Darling Cms based on whether or not any app components were found. */
if ((empty($appStartupObjects)) === true) {
    /* Temporarily enable appManager app for fresh install. */
    $appManager = new  \DarlingCms\classes\component\app('appManager');
    $appManager->enableApp();
    $appManager->registerTheme('appManager');
    /* Initialize singleAppStartup() object for appManager app, and add it to the $appStartupObjects array. */
    array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($appManager));
}

/* Initialize multiAppStartup() object, passing the first singleAppStartup object to the constructor. */
$mas = new  \DarlingCms\classes\startup\multiAppStartup(array_shift($appStartupObjects));

/* Add the rest of the app startup objects to the multiAppStartup object. */
foreach ($appStartupObjects as $appStartupObject) {
    $mas->setStartupObject($appStartupObject);
}

/* Startup the apps. */
$mas->startup();
