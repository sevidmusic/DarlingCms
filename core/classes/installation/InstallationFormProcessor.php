<?php

namespace DarlingCms\classes\installation;

use DarlingCms\abstractions\processor\AFormProcessor;
use DarlingCms\classes\config\DBConfig;
use DarlingCms\classes\config\RequiredSiteDBConfiguration;
use DarlingCms\classes\crud\SiteConfigurationFileCrud;
use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\classes\html\form\Hidden;
use DarlingCms\classes\html\HtmlTag;
use DarlingCms\classes\installer\DBInstaller;
use DarlingCms\classes\installer\Installer;
use DarlingCms\classes\installer\RequiredDirectoryInstaller;
use DarlingCms\classes\installer\SiteConfigurationFileInstaller;
use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\config\IDBConfig;
use DarlingCms\interfaces\installer\IInstaller;
use DarlingCms\interfaces\processor\IFormProcessor;

/**
 * Class InstallationFormProcessor. Defines an implementation of the IFormProcessor and IInstaller
 * interfaces that extends the AFormProcessor abstract class. This class can be used to process an
 * InstallationForm, and to perform installation of the site configuration file, site databases,
 * and site directories based on the submitted values of the InstallationForm being processed.
 * @package DarlingCms\classes\installation
 */
class InstallationFormProcessor extends AFormProcessor implements IFormProcessor, IInstaller
{
    /**
     * @var int Constant that can be passed to the InstallationFormProcessor::getDatabaseConfiguration()
     *          method to indicate that the Core database configuration should be returned.
     * @see InstallationFormProcessor::getDatabaseConfiguration()
     */
    const CORE_DB_CONFIG = 0;

    /**
     * @var int Constant that can be passed to the InstallationFormProcessor::getDatabaseConfiguration()
     *          method to indicate that the Apps database configuration should be returned.
     * @see InstallationFormProcessor::getDatabaseConfiguration()
     */
    const APPS_DB_CONFIG = 2;

    /**
     * @var int Constant that can be passed to the InstallationFormProcessor::getDatabaseConfiguration()
     *          method to indicate that the Users database configuration should be returned.
     * @see InstallationFormProcessor::getDatabaseConfiguration()
     */

    const USERS_DB_CONFIG = 4;

    /**
     * @var int Constant that can be passed to the InstallationFormProcessor::getDatabaseConfiguration()
     *          method to indicate that the Passwords database configuration should be returned.
     * @see InstallationFormProcessor::getDatabaseConfiguration()
     */

    const PASSWORDS_DB_CONFIG = 6;

    /**
     * @var int Constant that can be passed to the InstallationFormProcessor::getDatabaseConfiguration()
     *          method to indicate that the Privileges database configuration should be returned.
     * @see InstallationFormProcessor::getDatabaseConfiguration()
     */
    const PRIVILEGES_DB_CONFIG = 8;

    /**
     * @var Installer $installer Installer implementation instance used to perform installation
     *                           and un-installation of the site configuration file, site
     *                           databases, and site directories based on the submitted values
     *                           of the InstallationForm being processed.
     */
    private $installer;

    /**
     * InstallationFormProcessor constructor. Injects the InstallationForm instance processed by this
     * InstallationFormProcessor instance. Sets the Installer instance used to  perform installation
     * and un-installation of the site configuration file, site databases, and site directories based
     * on the submitted values of the InstallationForm being processed.
     * @param InstallationForm $installationForm The InstallationForm implementation instance that represents
     *                                           the Installation Form to be processed.
     */
    public function __construct(InstallationForm $installationForm)
    {
        parent::__construct($installationForm, new Hidden('installationFormId', $this->generateInstallationFormId($installationForm)));
        /* @devNote: Installer MUST only be set if form has been submitted! */
        if ($this->formSubmitted() === true) {
            $this->setInstaller(new Installer($this->getSiteConfigInstaller(), $this->getSiteDBInstaller(), $this->getSiteDirInstaller()));
        }
    }

    /**
     * Sets the Installer implementation instance used to perform installation and
     * un-installation of the site configuration file, site databases, and site
     * directories based on the submitted values of the InstallationForm being
     * processed.
     * @param Installer $installer The Installer implementation instance.
     */
    private function setInstaller(Installer $installer): void
    {
        $this->installer = $installer;
    }

    /**
     * Generates a form id for the installation form.
     *
     * Note: This is an ID  has nothing to do with the form's "id" html attribute.
     * This value is used as the value of a Hidden form element that is used to
     * identify the form after it has been submitted.
     *
     * WARNING: This method does not insure randomness, uniqueness, or entropy, do not
     * use it to generate an id that MUST be random, unique, or cryptographically secure.
     *
     * @param InstallationForm $installationForm The installation form to generate an id for.
     * @return string The id.
     */
    private function generateInstallationFormId(InstallationForm $installationForm): string
    {
        return trim(hash('sha512', substr(base64_encode(serialize($installationForm)), 0, 32)));
    }

    /**
     * Processes the form.
     * @return bool True if form was processed successfully, false otherwise.
     */
    public function processForm(): bool
    {
        if ($this->formSubmitted() === true && isset($this->installer) === true) { // @todo check installer implements...i.e., utilize class_implements()
            if ($this->install() === true) {
                // $this->form->addHtml(new HtmlTag('div', ['class' => 'dcms-loader']));
                $this->form->addHtml(new HtmlTag('p', ['class' => 'installer-positive-text installer-form-processor-msg'], 'Installation was successful. You will be redirected to your new <a href="' . CoreValues::getSiteRootUrl() . '">Darling Cms</a> site shortly.'));
                return true;
            }
            //$this->form->addHtml(new HtmlTag('div', ['class' => 'dcms-loader']));
            $this->form->addHtml(new HtmlTag('p', ['class' => 'installer-negative-text installer-form-processor-msg'], 'Installation failed. You will be redirected back to the <a href="' . CoreValues::getSiteRootUrl() . '">Installation form</a> shortly.'));
            error_log('Installation Form Processor Error: Installation failed.');
            return $this->uninstall();// attempt to perform un-installation on failure to make sure everything is cleaned up
        }
        return false;
    }

    /**
     * Get the specified submitted value.
     *
     * WARNING: This method will return an empty string if the specified value is not set, or
     * if the specified value is in fact an empty string. It is therefore not reliable to
     * test if the value returned by this method is empty to determine if this method was
     * successful.
     *
     * @param string $valueName The name of the value to get.
     * @return string The value. Note: This method will return an empty string if the
     *                specified value is not set, or if the specified value is in fact
     *                an empty string.
     */
    private function getSubmittedValue(string $valueName)
    {
        return trim((empty($this->getSubmittedValues()[$valueName]) === false ? $this->getSubmittedValues()[$valueName] : ''));
    }

    /**
     * Returns an IDBConfig implementation instance that represents the database configuration
     * indicated by the specified database configuration constant.
     *
     * Note: The IDBConfig implementation instances returned by this method are constructed using
     * the submitted InstallationForm's relevant database configuration values.
     *
     * @param int $configConstant The database configuration constant that indicates which database
     *                            configuration should be returned. MUST be one of the following:
     *
     *                                - InstallationFormProcessor::CORE_DB_CONFIG
     *
     *                                - InstallationFormProcessor::APPS_DB_CONFIG
     *
     *                                - InstallationFormProcessor::USERS_DB_CONFIG
     *
     *                                - InstallationFormProcessor::PASSWORDS_DB_CONFIG
     *
     *                                - InstallationFormProcessor::PRIVILEGES_DB_CONFIG
     * @return IDBConfig The IDBConfig implementation instance that represents the specified database
     *                   configuration.
     * @see InstallationFormProcessor::CORE_DB_CONFIG
     * @see InstallationFormProcessor::APPS_DB_CONFIG
     * @see InstallationFormProcessor::USERS_DB_CONFIG
     * @see InstallationFormProcessor::PASSWORDS_DB_CONFIG
     * @see InstallationFormProcessor::PRIVILEGES_DB_CONFIG
     * @see InstallationFormProcessor::getSubmittedValue()
     */
    private function getDatabaseConfiguration(int $configConstant): IDBConfig
    {
        switch ($configConstant) {
            case self::CORE_DB_CONFIG:
                return new DBConfig($this->getSubmittedValue('coreDatabaseName'), $this->getSubmittedValue('coreDatabaseHost'), $this->getSubmittedValue('coreDatabaseUserName'), $this->getSubmittedValue('coreDatabaseUserPassword'));
                break;
            case self::APPS_DB_CONFIG:
                return new DBConfig($this->getSubmittedValue('appsDatabaseName'), $this->getSubmittedValue('appsDatabaseHost'), $this->getSubmittedValue('appsDatabaseUserName'), $this->getSubmittedValue('appsDatabaseUserPassword'));
                break;
            case self::USERS_DB_CONFIG:
                return new DBConfig($this->getSubmittedValue('usersDatabaseName'), $this->getSubmittedValue('usersDatabaseHost'), $this->getSubmittedValue('usersDatabaseUserName'), $this->getSubmittedValue('usersDatabaseUserPassword'));
                break;
            case self::PASSWORDS_DB_CONFIG:
                return new DBConfig($this->getSubmittedValue('passwordsDatabaseName'), $this->getSubmittedValue('passwordsDatabaseHost'), $this->getSubmittedValue('passwordsDatabaseUserName'), $this->getSubmittedValue('passwordsDatabaseUserPassword'));
                break;
            case self::PRIVILEGES_DB_CONFIG:
                return new DBConfig($this->getSubmittedValue('privilegesDatabaseName'), $this->getSubmittedValue('privilegesDatabaseHost'), $this->getSubmittedValue('privilegesDatabaseUserName'), $this->getSubmittedValue('privilegesDatabaseUserPassword'));
                break;
            default:
                return new DBConfig('', '', '', '');
        }
    }

    /**
     * Returns an instance of a RequiredSiteDBConfiguration constructed from
     * the submitted InstallationForm's relevant database configurations.
     * @return RequiredSiteDBConfiguration
     */
    private function getRequiredSiteDBConfig(): RequiredSiteDBConfiguration
    {
        return new RequiredSiteDBConfiguration(
            $this->getDatabaseConfiguration(self::CORE_DB_CONFIG),
            $this->getDatabaseConfiguration(self::APPS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::USERS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::PASSWORDS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::PRIVILEGES_DB_CONFIG)
        );
    }

    /**
     * Perform installation.
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool
    {
        return $this->installer->install();
    }

    /**
     * Get the SiteConfigurationFileInstaller implementation instance used to install the site
     * configuration file.
     * @return SiteConfigurationFileInstaller The SiteConfigurationFileInstaller implementation
     *                                        instance used to install the site configuration file.
     */
    private function getSiteConfigInstaller(): SiteConfigurationFileInstaller
    {
        return new SiteConfigurationFileInstaller(new SiteConfigurationFileCrud(CoreValues::getSiteConfigPath()), $this->getRequiredSiteDBConfig());

    }

    /**
     * Returns the DBInstaller implementation instance used to install the configured databases.
     * @return DBInstaller The DBInstaller implementation instance used to install the configured databases.
     */
    public function getSiteDBInstaller(): DBInstaller
    {
        // @todo ! THIS WILL ONLY WORK ON LOCAL -> NEED TO IMPLEMENT FOR LIVE SERVER....
        /* @todo root MySql user/password should be gotten from form submission....i.e., ask user to provide correct credentials. */
        return new DBInstaller(
            new MySqlQuery("mysql:host=localhost", 'root', 'root'),
            $this->getDatabaseConfiguration(self::CORE_DB_CONFIG),
            $this->getDatabaseConfiguration(self::APPS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::USERS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::PASSWORDS_DB_CONFIG),
            $this->getDatabaseConfiguration(self::PRIVILEGES_DB_CONFIG)
        );
    }

    /**
     * Returns the RequiredDirectoryInstaller implementation instance used to install
     * the required directories.
     * @return RequiredDirectoryInstaller The RequiredDirectoryInstaller implementation instance used to install
     *                                    the required directories.
     */
    private function getSiteDirInstaller(): RequiredDirectoryInstaller
    {
        return new RequiredDirectoryInstaller();
    }

    /**
     * Perform un-installation.
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        // only attempt un-installation if installer has been set
        if (isset($this->installer) === true) {
            return $this->installer->uninstall();
        }
        return false;
    }
}
