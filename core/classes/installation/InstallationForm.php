<?php

namespace DarlingCms\classes\installation;

use DarlingCms\classes\html\form\Form;
use DarlingCms\classes\html\form\Password;
use DarlingCms\classes\html\form\Submit;
use DarlingCms\classes\html\form\Text;
use DarlingCms\classes\html\HtmlTag;
use DarlingCms\interfaces\html\IHtmlForm;

/**
 * Class InstallationForm. Defines an implementation of the IHtmlForm interface that
 * extends the Form class. Instances of this class can be used to construct the html
 * form used to gather the information required to install a Darling Cms site.
 * @package DarlingCms\classes\installation
 */
class InstallationForm extends Form implements IHtmlForm
{
    /**
     * InstallationForm constructor. Constructs the installation form.
     *
     * Note: This implementation's __construct() method does not accept any parameters.
     * Doc-comments are reflective of the parent Form class.
     */
    public function __construct()
    {
        parent::__construct(Form::POST, array('id' => 'dcms-installation-form', 'class' => 'dcms-installation-form dcms-form'));
        $this->buildForm();
    }

    /**
     * Builds the form.
     * @return void
     */
    private function buildForm(): void
    {
        $this->buildWelcomeMsg();
        $this->buildAdminConfigSection();
        $this->buildSubmitButton();
        $this->buildConfigSectionMenu();
        $this->buildDBConfigSection();
    }

    /**
     * Builds the form's welcome message.
     * @return void
     */
    private function buildWelcomeMsg(): void
    {
        $this->addHtml(new HtmlTag('p', array('class' => 'installer-text'), 'Please use the form below to configure your new Darling Cms Site.'));
        $this->addHtml(new HtmlTag('p', array('class' => 'installer-negative-text installer-small-text'), 'Note: All fields are required!'));
    }

    /**
     * Builds the form's administrative user registration section.
     * @return void
     */
    private function buildAdminConfigSection(): void
    {
        $this->addHtml(new HtmlTag('h3', ['id' => 'AdminUserRegistration'], 'Admin User Registration'));
        $this->buildAdminConfigFormElements();
        $this->addHtml(new HtmlTag('br', [], '', true));
        $this->addHtml(new HtmlTag('br', [], '', true));
    }

    /**
     * Builds the form's administrative user registration section's form elements.
     * @return void
     */
    private function buildAdminConfigFormElements(): void
    {
        $this->addFormElement(new Text('adminUserName', '', array('id' => 'dcms-install-form-admin-user-name', 'class' => 'dcms-install-form-text-input', 'placeholder' => 'Enter a username', 'autocomplete' => 'off', 'required'), true));
        $this->addFormElement(new Text('adminUserEmail', '', array('id' => 'dcms-install-form-admin-user-email', 'class' => 'dcms-install-form-text-input', 'placeholder' => 'Enter Your Email', 'autocomplete' => 'off', 'required'), true));
        $this->addFormElement(new Password('adminUserPassword', '', array('id' => 'dcms-install-form-admin-user-password', 'class' => 'dcms-install-form-password-input', 'placeholder' => 'Enter a Password', 'autocomplete' => 'off', 'required'), true));
    }

    /**
     * Builds the form's submit button.
     * @return void
     */
    private function buildSubmitButton(): void
    {
        $this->addFormElement(new Submit('install', 'Proceed with installation?', array('class' => 'dcms-install-form-submit')));
    }

    /**
     * Builds the form's database configuration section.
     * @return void
     */
    private function buildDBConfigSection(): void
    {
        $this->addHtml(new HtmlTag('h3', [], 'Database Configuration(s)'));
        foreach ($this->getDBInfoFormElementDescriptors() as $dbInfoFormElementDescriptor) {
            $this->addHtml(new HtmlTag('div', ['id' => $dbInfoFormElementDescriptor . 'DatabaseConfigForm', 'class' => 'installer-db-info-element-group'], '', true));
            $this->addHtml(new HtmlTag('h3', [], ucfirst($dbInfoFormElementDescriptor) . ' Database Configuration:'));
            $this->buildDBConfigFormElementGroup($dbInfoFormElementDescriptor);
            $this->addHtml(new HtmlTag('br', [], '', true));
            $this->addHtml(new HtmlTag('/div', [], '', true));
        }
    }


    /**
     * Returns an array of the from element descriptors used to identify
     * the required database configurations.
     * @return array Array of the from element descriptors used to identify
     *               the required database configurations.
     */
    private function getDBInfoFormElementDescriptors(): array
    {
        return array('core', 'apps', 'users', 'passwords', 'privileges');
    }

    /**
     * Builds the form's configuration section menu.
     * @return void
     */
    private function buildConfigSectionMenu(): void
    {
        $this->addHtml(new HtmlTag('div', ['id' => 'DatabaseConfigFormLinks', 'class' => 'installer-db-config-form-links'], '', true));
        $this->addHtml(new HtmlTag('a', ['href' => '#AdminUserRegistration'], 'Admin User Registration'));
        foreach ($this->getDBInfoFormElementDescriptors() as $dbInfoFormElementDescriptor) {
            $this->addHtml(new HtmlTag('a', ['href' => '#' . $dbInfoFormElementDescriptor . 'DatabaseConfigForm'], ucfirst($dbInfoFormElementDescriptor) . ' Database Configuration'));
        }
        $this->addHtml(new HtmlTag('/div', [], '', true));
    }

    /**
     * Builds a database configuration section form element group based on the specified element descriptor.
     * @param string $dbInfoFormElementDescriptor The form element descriptor for the form element group to build.
     */
    private function buildDBConfigFormElementGroup(string $dbInfoFormElementDescriptor): void
    {
        $this->addFormElement(new Text($dbInfoFormElementDescriptor . 'DatabaseName', '', array('id' => 'dcms-install-form-' . $dbInfoFormElementDescriptor . '-db-name', 'class' => 'dcms-install-form-text-input', 'placeholder' => 'Enter the name of the database to use for ' . ucfirst($dbInfoFormElementDescriptor), 'required'), true));
        $this->addFormElement(new Text($dbInfoFormElementDescriptor . 'DatabaseHost', 'localhost', array('id' => 'dcms-install-form-' . $dbInfoFormElementDescriptor . '-db-host', 'class' => 'dcms-install-form-text-input', 'placeholder' => 'Enter the name of the host for the ' . ucfirst($dbInfoFormElementDescriptor) . ' database', 'required'), true));
        $this->addFormElement(new Text($dbInfoFormElementDescriptor . 'DatabaseUserName', '', array('id' => 'dcms-install-form-' . $dbInfoFormElementDescriptor . '-db-user-name', 'class' => 'dcms-install-form-text-input', 'placeholder' => 'Enter the user name to assign the ' . ucfirst($dbInfoFormElementDescriptor) . ' db user.', 'required'), true));
        $this->addFormElement(new Password($dbInfoFormElementDescriptor . 'DatabaseUserPassword', '', array('id' => 'dcms-install-form-' . $dbInfoFormElementDescriptor . '-db-user-password', 'class' => 'dcms-install-form-password-input', 'placeholder' => 'Enter the password to assign the ' . ucfirst($dbInfoFormElementDescriptor) . ' db user.', 'required'), true));
    }
}
