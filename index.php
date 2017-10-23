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
    /* Instantiate a new ReflectionClass object for the multiAppStartup object. This will allow the $appStartupObjects
       array to be passed to the multiAppStartup object's constructor whole, instead of shifting off the first item, and
       then iterating through and passing the rest of array's items to setStartupObject(). Essentially, this removes the
       need for a loop. @see https://stackoverflow.com/questions/3395914/pass-arguments-from-array-in-php-to-constructor
    */
    /* Instantiate ReflectionClass object for the multiAppStartup instance. */
    $reflector = new ReflectionClass('DarlingCms\classes\startup\multiAppStartup');
    /* Call the ReflectionClass's newInstanceArgs() method to pass the appStartupObjects in the $adppStarupObjects
       array to the multiAppStartup object's constructor. This logic removes the need to loop through the array. */
    $mas = $reflector->newInstanceArgs($appStartupObjects);
    /* Startup the apps. */
    $mas->startup();
    /* Original Code | Left for reference. Does the same thing as the code above but uses a loop to iterate
       through the appStarupObjects, passing each one to setStartupObject individually after shifting off
       the first appStartupObject upon instantiation of a new multiAppStartupObject.
    /* Initialize multiAppStartup() object, passing the first singleAppStartup object to the constructor. *
    $mas = new  \DarlingCms\classes\startup\multiAppStartup(array_shift($appStartupObjects));
    /* Add the rest of the app startup objects to the multiAppStartup object. *
    foreach ($appStartupObjects as $appStartupObject) {
        $mas->setStartupObject($appStartupObject);
    }
    /* Startup the apps. *
    $mas->startup();
    */
}
