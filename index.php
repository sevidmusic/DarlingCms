<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

/* Instantiate Darling Cms Startup object. */
$darlingCmsStartup = new \DarlingCms\classes\startup\darlingCmsStartup(new \DarlingCms\classes\startup\appStartup(), new \DarlingCms\classes\startup\themeStartup());

/* Startup the Darling Cms. */
$darlingCmsStartup->startup();
