<?php
/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');
/** Initialize the user interface. **/
$CoreHtmlUserInterface = new \DarlingCms\classes\userInterface\CoreHtmlUserInterface(new \DarlingCms\classes\startup\MultiAppStartup(new \DarlingCms\classes\info\AppInfo(\DarlingCms\classes\info\AppInfo::STARTUP_DEFAULT)));
/** Display the user interface. **/
echo $CoreHtmlUserInterface->getUserInterface();
