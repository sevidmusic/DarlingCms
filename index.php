<?php
/** Require Composer's auto-loader. **/
require(__DIR__ . '/vendor/autoload.php');
// Determine which UI to load based on whether or not the site has been configured
switch (\DarlingCms\classes\staticClasses\core\CoreValues::siteConfigured()) {
    case false:
        // @todo: Implement installer so user can be guided through configuration process via UI...
        echo "
            <h1>Welcome to the Darling Cms</h1>
            <p>Sorry, it looks like your Darling Cms site has not been configured.</p>
            <p>Please create a configuration file to use your new Darling Cms installation.</p>
            ";
        break;
    default:
        /** Initialize the user interface. **/
        $CoreHtmlUserInterface = new \DarlingCms\classes\userInterface\CoreHtmlUserInterface(new \DarlingCms\classes\startup\MultiAppStartup(new \DarlingCms\classes\info\AppInfo(\DarlingCms\classes\info\AppInfo::STARTUP_DEFAULT)));
        /** Display the user interface. **/
        echo $CoreHtmlUserInterface->getUserInterface();
        break;
}

