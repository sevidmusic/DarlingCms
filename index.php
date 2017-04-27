<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */
$pageName = ucwords(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING));
$rootUrl = __DIR__ . '/';
echo "<!DOCTYPE html>
<html>
  <head>
    <title>$pageName</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/darlingCms/darlingCms.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/helloWorld/helloWorld.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/helloUniverse/helloUniverse.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/crudTester/crudTester.css\">
  </head>
  <body>
";
/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

$app1 = new \DarlingCms\classes\component\app('varDumper');
$app1->enableApp();
$app2 = new \DarlingCms\classes\component\app('helloWorld');
$app2->enableApp();
$app3 = new \DarlingCms\classes\component\app('crudTester');
$app3->enableApp();
$app4 = new \DarlingCms\classes\component\app('phpCanvas');
$app4->enableApp();

$singleAppStartup1 = new \DarlingCms\classes\startup\singleAppStartup($app1);
$singleAppStartup2 = new \DarlingCms\classes\startup\singleAppStartup($app2);
$singleAppStartup3 = new \DarlingCms\classes\startup\singleAppStartup($app3);
$singleAppStartup4 = new \DarlingCms\classes\startup\singleAppStartup($app4);

$multiAppStartup = new \DarlingCms\classes\startup\multiAppStartup($singleAppStartup1, $singleAppStartup2);
$multiAppStartup->setStartupObject($singleAppStartup3);
$startupObject = new \DarlingCms\classes\startup\darlingCmsStartup($multiAppStartup);
$startupObject->setStartupObject($singleAppStartup4);
$startupObject->startup();
if (function_exists('huDebug')) {
    huDebug($startupObject, 'Startup Object');
}
echo "
  </body>
</html>
";

