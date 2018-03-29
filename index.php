<?php
/**
 * WARNING: DO NOT OUTPUT ANYTHING TO PAGE UNTIL THE multiAppStartup OBJECT'S startup() METHOD HAS BEEN CALLED.
 * DOING SO MAY CAUSE PHP TO COMPLAIN THAT HEADERS WERE ALREADY SENT IF ANY APPS USE SESSIONS. IT IS OK TO
 * OUTPUT TO THE PAGE AFTER CALL TO multiAppStartup OBJECT'S startup() METHOD.
 */

/*** Require Composer's auto-loader. ***/
require(__DIR__ . '/vendor/autoload.php');

/*** Initialize the Darling Cms. ***/
$initializer = new \DarlingCms\classes\initializer\dcmsInitializer(new \DarlingCms\classes\crud\registeredJsonCrud());
$initializer->initialize();
/* Get the initialized items. */
$initialized = $initializer->getInitialized();
/* If the multiAppStartup object was initialized, proceed, otherwise do nothing because this is a fresh install.
   Note: Fresh installations are handled by internally by the dcmsInitializer object. */
if (isset($initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup']) && gettype($initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup']) === 'object' && get_class($initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup']) === 'DarlingCms\classes\startup\multiAppStartup') {
    /*** Instantiate the htmlHead object used to generate the pages html header. ***/
    /**
     * NOTE: It is best to instantiate the htmlHead object before app startup to insure changes made to htmlhead
     * object by apps take precedence over index.php. If instantiated after startup then changes to htmlHead made
     * by index.php will take precedence over changes made by apps. Furthermore, changes made by index.php in
     * general should happen before startup or they will take precedence over apps.
     * e.g. calling resetHtmlHead() before startup will allow changes made by apps to apply, if called after
     * startup then any changes made by apps will not apply.
     */
    $htmlHead = new \DarlingCms\classes\component\html\htmlHead($initializer->getCrud());
    $htmlHead->prependHtml(new \DarlingCms\classes\component\html\html('meta', '', array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0')));
    /* Get the multiAppStartup object. */
    $multiAppStartupObject = $initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup'];
    /* Use the multiAppStartup object to startup the apps | * SEE WARNING ABOVE ABOUT OUTPUTTING FROM index.php. */
    $multiAppStartupObject->startup();
    /*** Display the page. | * SEE WARNING ABOVE ABOUT OUTPUTTING FROM index.php. ***/
    /* Output opening html | doctype, opening html tag, etc. */
    echo '<!DOCTYPE html>' . PHP_EOL . '<html>' . $htmlHead . '<body>' . PHP_EOL;
    /* Display app output from any apps that were started up. */
    echo $multiAppStartupObject->getAppOutput();
    /* Output closing html | closing body tag, closing html tag, etc.*/
    echo PHP_EOL . '</body>' . PHP_EOL . '</html>';
}
