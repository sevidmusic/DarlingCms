<?php
/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');

use DarlingCms\classes\info\AppInfo;
use DarlingCms\classes\installation\InstallationForm;
use DarlingCms\classes\installation\InstallationFormProcessor;
use DarlingCms\classes\startup\MultiAppStartup;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\classes\userInterface\CoreInstallerUI;
use DarlingCms\classes\userInterface\CoreHtmlUserInterface;

// Determine which UI to load based on whether or not the site has been configured
switch (CoreValues::siteConfigured()) {
    case false:
        $installationFormProcessor = new InstallationFormProcessor(new InstallationForm());
        $installationFormProcessor->processForm();
        $installerUI = new CoreInstallerUI($installationFormProcessor->getForm());
        echo $installerUI->getUserInterface();
        break;
    default:
        /** Initialize the user interface. **/
        $CoreHtmlUserInterface = new CoreHtmlUserInterface(new MultiAppStartup(new AppInfo(AppInfo::STARTUP_DEFAULT)));
        /** Display the user interface. **/
        echo $CoreHtmlUserInterface->getUserInterface();
        break;
}

CoreValues::getSiteRootUrl();
