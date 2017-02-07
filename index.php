<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/* DEV CODE | REMOVE THIS CODE ON PRODUCTION SITES!!! */
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Startup Objects */
$appStartup = new \DarlingCms\classes\startup\appStartup();
$themeStartup = new \DarlingCms\classes\startup\themeStartup();

$startupObjects = array($appStartup, $themeStartup);
foreach($startupObjects as $startupObject) {
    $startupObject->errorReporting(true);
    $startupObject->startup();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    $themeStartup->displayThemeLinks();
    ?>
</head>
<body>
<?php
foreach ($appStartup->runningApps() as $runningApp) {
    $appStartup->displayAppOutput($runningApp);
}
?>
</body>
</html>
