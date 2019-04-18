<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-07
 * Time: 11:16
 */

namespace DarlingCms\classes\userInterface;

use DarlingCms\classes\installation\InstallationForm;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\html\IHtmlPage;
use DarlingCms\interfaces\userInterface\IUserInterface;

/**
 * Class CoreInstallerUI. Defines an implementation of the IUserInterface, and IHtmlPage interfaces
 * that can be used to generate the user interface for the Darling Cms installer.
 * @package DarlingCms\classes\userInterface
 */
class CoreInstallerUI implements IUserInterface, IHtmlPage
{
    /**
     * @var InstallationForm $installationForm Instance of an InstallationForm that represents the
     *                                         installation form.
     */
    private $installationForm;

    /**
     * CoreInstallerUI constructor.
     * @param InstallationForm $installationForm
     */
    public function __construct(InstallationForm $installationForm)
    {
        $this->installationForm = $installationForm;
    }

    /**
     * Gets the user interface.
     * @return string The user interface.
     */
    public function getUserInterface(): string
    {
        return $this->getDoctype() . '<html lang="en" style="font-size: 24px;">' . $this->getHead() . $this->getBody() . '</html>';
    }

    /**
     * Returns the Doctype.
     * @return string The Doctype.
     */
    public function getDoctype(): string
    {
        return '<!DOCTYPE html>' . PHP_EOL;
    }

    /**
     * Returns the head.
     * @return string The head.
     */
    public function getHead(): string
    {
        // @devNote @todo Note on meta refresh, may not want to redirect once everything is working so as not to accidentally interrupt install.
        return PHP_EOL . '
<head>
    <title>Darling Cms Installer | Welcome To The Darling Cms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="refresh" content="25; url=' . CoreValues::getSiteRootUrl() . '" /> -->
    <style>
   * {
    box-sizing: border-box;
    -webkit-box-decoration-break: clone;
    -o-box-decoration-break: clone;
    box-decoration-break: clone;
}

body {
    /* Original Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#959595+0,2a3c51+18,0d0d0d+36,2a3542+50,0a0a0a+69,4e4e4e+80,383838+87,1b1b1b+100 */
    /* Last Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#02060c+0,2a3c51+18,0d0d0d+36,2a3542+50,0a0a0a+67,07160b+80,141414+90,1b1b1b+100 */
    /* Current Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#02060c+0,2a3c51+18,0d0d0d+36,2a3542+50,0a0a0a+67,061014+80,141414+90,1b1b1b+100 */
    background: rgb(2, 6, 12); /* Old browsers */
    background: -moz-linear-gradient(45deg, rgba(2, 6, 12, 1) 0%, rgba(42, 60, 81, 1) 18%, rgba(13, 13, 13, 1) 36%, rgba(42, 53, 66, 1) 50%, rgba(10, 10, 10, 1) 67%, rgba(6, 16, 20, 1) 80%, rgba(20, 20, 20, 1) 90%, rgba(27, 27, 27, 1) 100%); /* FF3.6-15 */
    background: -webkit-linear-gradient(45deg, rgba(2, 6, 12, 1) 0%, rgba(42, 60, 81, 1) 18%, rgba(13, 13, 13, 1) 36%, rgba(42, 53, 66, 1) 50%, rgba(10, 10, 10, 1) 67%, rgba(6, 16, 20, 1) 80%, rgba(20, 20, 20, 1) 90%, rgba(27, 27, 27, 1) 100%); /* Chrome10-25,Safari5.1-6 */
    background: linear-gradient(45deg, rgba(2, 6, 12, 1) 0%, rgba(42, 60, 81, 1) 18%, rgba(13, 13, 13, 1) 36%, rgba(42, 53, 66, 1) 50%, rgba(10, 10, 10, 1) 67%, rgba(6, 16, 20, 1) 80%, rgba(20, 20, 20, 1) 90%, rgba(27, 27, 27, 1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\' #02060c\', endColorstr=\' #1b1b1b\', GradientType=1); /* IE6-9 fallback on horizontal gradient */
    color: #5786aa;
    font-size: 21px;
    font-family: monospace;
    margin: 20px;
}

h3 {
    color: #326762;
}

.installer-db-config-form-links a {
    font-size: .7em;
    text-decoration: none;
    color: #5fa0e1;
    background: #2A3542;
    width: 27%;
    display: inline-block;
    border: 3px double #ffffff;
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
    padding: 20px;
    margin-right: 6.1%;
    min-width: 350px;
    overflow: auto;
}

/* unvisited link */
.installer-db-config-form-links a:link {
}

/* visited link */
.installer-db-config-form-links a:visited {
}

/* mouse over link */
.installer-db-config-form-links a:hover {
    color: #2a4663;
    background: #b9d9cf;
    border: 3px solid #2a4663;
}

/* selected link */
.installer-db-config-form-links a:active {
}

/* unvisited link */
.installer-db-config-form-links a, .installer-db-config-form-links a:link, .installer-db-config-form-links a:visited, .installer-db-config-form-links a:hover, .installer-db-config-form-links a:active {

}


label {
    color: #76ff7e;
    text-shadow: 2px 2px #23372b;
    line-height: 3;
}

.installer-db-config-form-links {
    position: sticky;
    top: 70px;
    z-index: 1000;
}

.installer-small-text {
    font-size: .7em;
}

.installer-text {
    color: #8EA3B0;
}

.installer-negative-text {
    color: #b0292e;
    line-height: 0;
}

.installer-positive-text {
    color: #47b074;
}

.installer-db-info-element-group {
    background: #030505;
    margin-top: 20px;
    margin-bottom: 75px;
    padding: 50px;
    border: 2px double #ffffff;
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
    opacity: .61;
}

.dcms-install-form-submit, .dcms-install-form-password-input, .dcms-install-form-text-input {
    background: #000000;
    color: #8EA3B0;
    padding: 15px;
    font-size: 1.2em;
    font-family: monospace;
    margin-right: 20px;
}

.dcms-install-form-password-input, .dcms-install-form-text-input {
    width: 100%;
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
}

.dcms-install-form-submit {
    width: 100%;
    position: sticky;
    top: 0;
    z-index: 1000;
    -webkit-border-radius: 20px;
    -moz-border-radius: 20px;
    border-radius: 20px;
    margin-bottom: 2%;
}

.dcms-loader {
    animation: spin 2s linear infinite;
    background-color: #000000;
    background-image: url("' . CoreValues::getSiteRootUrl() . 'themes/DCMSBase/images/DcmsLoaderBg1.png");
    background-repeat: no-repeat;
    background-position: center;
    border: 3px double #18623b;
    border-radius: 72%;
    border-top: 3px double #185a33;
    border-right: 3px double #123e2e;
    /*border-right: 3px double #080f19;*/
    border-bottom: 3px double #11372a;
    /*border-bottom: 3px double #ff7138;*/
    width: 62px;
    height: 62px;
    position: fixed;
    top: 36%;
    left: 47%;
}

.installer-form-processor-msg {
    /* animated properties */
    opacity: 0;
    padding: 4%;
    width: 73%;
    text-shadow: 2px 2px #050a10;
    background-color: #68707c;
    /* not anmiated */
    border: 3px double #ffffff; /*#1C3D53;*/
    border-radius: 2%;
    position: sticky;
    bottom: 420px;
    z-index: 1000;
    margin: 0 auto;
    animation: glow 12s ease infinite;
    display: block;
}

.installer-form-processor-msg a {
    color: #ffffff;
    text-decoration: none;
}

.installer-form-processor-msg a:link {
}

.installer-form-processor-msg a:visited {
}

.installer-form-processor-msg a:hover {
    color: #5fa0e1;
}

.installer-form-processor-msg a:active {
}


@keyframes glow {
    0% {
        opacity: 0;
        padding: 4%;
        width: 73%;
        text-shadow: 2px 2px #050a10;
        background-color: #68707c;
    }
    17% {
        opacity: .97;
        padding: 8%;
        width: 81%;
        text-shadow: 2px 2px #1d1d24;
        background-color: #02060C;
    }

    42% {
        opacity: .85;
        padding: 8%;
        width: 81%;
        text-shadow: 2px 2px #1d1d24;
        background-color: #02060C;
    }
    80% {
        opacity: 0;
        padding: 4%;
        width: 73%;
        text-shadow: 2px 2px #050a10;
        background-color: #68707c;
    }
    100% {
        opacity: 0;
        padding: 4%;
        width: 73%;
        text-shadow: 2px 2px #050a10;
        background-color: #68707c;
    }

}

@keyframes spin {
    0% {
        opacity: 1;
        transform: rotate(0deg);
    }
    50% {
        opacity: 0;
    }

    100% {
        transform: rotate(360deg);
        opacity: 1;
    }
}
    </style>
</head>
' . PHP_EOL;
    }

    /**
     * Returns the body.
     * @return string The body.
     */
    public function getBody(): string
    {
        return PHP_EOL . '<body>' . PHP_EOL . $this->getWelcomeMsg() . PHP_EOL . $this->installationForm->getHtml() . PHP_EOL . '</body>' . PHP_EOL;
    }

    /**
     * Gets the string of html for the Core Installer UI's welcome message.
     * @return string The html for the welcome message.
     */
    private function getWelcomeMsg(): string
    {
        return PHP_EOL . "<h1>Welcome to the Darling Cms</h1>" . PHP_EOL;
    }

}
