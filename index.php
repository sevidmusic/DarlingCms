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

if (empty($appStartupObjects) === false) {
    /* Instantiate ReflectionClass object for the multiAppStartup instance. */
    $reflector = new ReflectionClass('DarlingCms\classes\startup\multiAppStartup');
    /* Call the ReflectionClass's newInstanceArgs() method to pass the singleAppStartup objects in the $appStartupObjects
       array to the multiAppStartup object's constructor. This logic removes the need to loop through the array. */
    $mas = $reflector->newInstanceArgs($appStartupObjects);
    /* Startup the apps. */
    $mas->startup();
}
