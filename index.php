<?php
/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');
$CoreHtmlUserInterface = new \DarlingCms\classes\userInterface\CoreHtmlUserInterface(new \DarlingCms\classes\startup\MultiAppStartup());
echo $CoreHtmlUserInterface->getUserInterface();
