<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/* DEV CODE */
function displayDevFooter()
{

    $buttonStyles = "top: 0;position:fixed;display: block;";

    $duration = "slow";
    echo "
      <button style='$buttonStyles' id='hideLinks'>Show / Hide Links</button>
      <script>
        $( \"#hideLinks\" ).click(function() {
        $( \"a\" ).fadeToggle( \"$duration\" );
      });
    </script>
    
    <button style='{$buttonStyles}left:10%;' id='hideContent'>Show / Hide Content</button>
      <script>
        $( \"#hideContent\" ).click(function() {
        $( \"p\" ).slideToggle( \"$duration\" );
        $( \"div\" ).slideToggle( \"$duration\" );
        $( \"h1\" ).slideToggle( \"$duration\" );
        $( \"h2\" ).slideToggle( \"$duration\" );
        $( \"h3\" ).slideToggle( \"$duration\" );
        $( \"h4\" ).slideToggle( \"$duration\" );
        $( \"h5\" ).slideToggle( \"$duration\" );
        $( \"h6\" ).slideToggle( \"$duration\" );
      });
    </script>    
    
    <button style='{$buttonStyles}left:20%;' id='hideDivs'>Show / Hide Divs</button>
      <script>
        $( \"#hideDivs\" ).click(function() {
        $( \"div\" ).fadeToggle( \"$duration\" );
      });
    </script>
    
    <button style='{$buttonStyles}left:30%;' id='hideTable'>Show / Hide Tables</button>
      <script>
        $( \"#hideTable\" ).click(function() {
        $( \"table\" ).slideToggle( \"$duration\" );
      });
    </script>
    
    <button style='{$buttonStyles}left:40%;' id='hideMenus'>Show / Hide Side Menus</button>
      <script>
        $( \"#hideMenus\" ).click(function() {
        $( \"#helloUniverse\" ).slideToggle( \"$duration\" );
        $( \"#helloWorld\" ).slideToggle( \"$duration\" );
      });
    </script>
    
  </body>
</html>
";

}

function displayDevHead()
{
    $pageName = ucwords(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING));

    echo "<!DOCTYPE html>
<html>
  <head>
    <title>$pageName</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/appManager/appManager.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/darlingCms/darlingCms.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/helloWorld/helloWorld.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/helloUniverse/helloUniverse.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/crudTester/crudTester.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/varDumper/varDumper.css\">
    <script src=\"https://code.jquery.com/jquery-1.10.2.js\"></script>
  </head>
  <body>
";
}

displayDevHead();

/* END DEV CODE */
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
$apps = array('varDumper', 'helloUniverse', 'helloWorld', 'crudTester', 'phpCanvas');

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

displayDevFooter();
