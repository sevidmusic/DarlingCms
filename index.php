<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

$startupObject = new \DarlingCms\classes\startup\darlingCmsStartup(new \DarlingCms\classes\crud\registeredJsonCrud());
$startupObject->startup();
