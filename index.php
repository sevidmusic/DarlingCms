<?php
/* DEV CODE | REMOVE THIS CODE ON PRODUCTION SITES!!! */
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

$appStartup = new \DarlingCms\classes\startup\appStartup();
$appStartup->errorReporting(true);
$startupObjects = array($appStartup);
foreach($startupObjects as $startupObject) {
    $startupObject->startup();
}
foreach($appStartup->runningApps() as $runningApp) {
    $appStartup->displayAppOutput($runningApp);
}
