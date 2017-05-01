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

/* Initialize array of the names of the apps to startup. */
$apps = array('htmlHeadManager', 'varDumper', 'helloUniverse', 'helloWorld', 'crudTester', 'cveReporter', 'phpCanvas', 'htmlFooterManager');

$appStartupObjects = array();

foreach ($apps as $appName) {
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
