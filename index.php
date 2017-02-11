<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/* DEV CODE | REMOVE THIS CODE ON PRODUCTION SITES!!! */ini_set('xdebug.var_display_max_depth', -1);ini_set('xdebug.var_display_max_children', -1);ini_set('xdebug.var_display_max_data', -1);

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Instantiate Darling Cms Startup object. */
$darlingCmsStartup = new \DarlingCms\classes\startup\darlingCmsStartup(new \DarlingCms\classes\startup\appStartup(), new \DarlingCms\classes\startup\themeStartup());

/* Startup the Darling Cms. */
$darlingCmsStartup->startup();
