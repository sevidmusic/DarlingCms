<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/* NOTE: At the moment the code in the index file is just dev code. Until the various
         core objects are finished, the logic of the index file will remain in dev. */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Ensure appManager is always loaded, for the moment it is responsible for initializing core apps. */
$appManager = new  \DarlingCms\classes\component\app('appManager');
$appManager->enableApp();
$appManager->registerTheme('appManager');

/* Initialize singleAppStartup() object for appManager app. */
$aps = new \DarlingCms\classes\startup\singleAppStartup($appManager);

/* Initialize multiAppStartup() object. */
$mas = new  \DarlingCms\classes\startup\multiAppStartup($aps);

/* Initialize crud to read app components from storage. */
$crud = new \DarlingCms\classes\crud\registeredJsonCrud();

/* Determine the names of the available apps in the apps directory. */
$availableApps = array();
foreach (new DirectoryIterator(__DIR__ . '/apps') as $fileInfo) {
    if ($fileInfo->isDot()) {
        continue;
    }
    $availableApps[] = $fileInfo->getFilename();
}

$appStartupObjects = array();

foreach ($availableApps as $appName) {
    if ($crud->read($appName) !== false) {
        $app = $crud->read($appName);
        array_push($appStartupObjects, new \DarlingCms\classes\startup\singleAppStartup($app));
    }
}

foreach ($appStartupObjects as $appStartupObject) {
    $mas->setStartupObject($appStartupObject);
}

$mas->startup();

/*
 *  NOTE : The following code best reflects what the final logic of the index file will look like.

/* Initialize darlingCmsStartup() object * /
$su = new \DarlingCms\classes\startup\darlingCmsStartup($mas);

/* Startup the Darling Cms.  * /
$su->startup();

*/
