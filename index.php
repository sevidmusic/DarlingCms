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
     * WARNING: It is best to instantiate the htmlHead object before app startup to insure changes made to htmlhead
     * object by apps take precedence over index.php. If instantiated after startup then changes to htmlHead made
     * by index.php will take precedence over changes made by apps. Furthermore, changes made by index.php in
     * general should happen before startup or they will take precedence over apps.
     */
    $htmlHead = new \DarlingCms\classes\component\html\htmlHead($initializer->getCrud());
    /* Create a meta tag to set the viewport and prepend it to the htmlHead's html container. */
    $htmlHead->prependHtml(new \DarlingCms\classes\component\html\html('meta', '', array('name="viewport"', 'content="width=device-width, initial-scale=1.0"')));
    /* Create the user interface. */
    $userInterface = new \DarlingCms\classes\userInterface\userInterface();
    /* Set the user interface's container type to "html". */
    $userInterface->containerType = 'html';
    /* Add the htmlHead's html container to the user interface's opening html. */
    $userInterface->addOpeningHtml($htmlHead);
    /* Add html comment to the userInterface's closing html with the message:
       Site created with the Darling Cms | View on GitHub @ http://www.github.com/sevidmusic/DarlingCms */
    $userInterface->addClosingHtml(new \DarlingCms\classes\component\html\html('!--', 'Site created with the Darling Cms | View on GitHub @ http://www.github.com/sevidmusic/DarlingCms'));
    /* Get the multiAppStartup object. */
    $multiAppStartupObject = $initialized['DarlingCms\classes\startup\multiAppStartup']['multiAppStartup'];
    /* Use the multiAppStartup object to startup the apps | * SEE WARNING ABOVE ABOUT OUTPUTTING FROM index.php. */
    $multiAppStartupObject->startup();
    /* Create the body html tag, and assign the app output as it's content. */
    $body = new \DarlingCms\classes\component\html\html('body', $multiAppStartupObject->getAppOutput());
    echo '<!DOCTYPE html>' . $userInterface->getUserInterface($body);
}
