<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/26/18
 * Time: 10:48 AM
 */

namespace DarlingCms\classes\html\form;

use DarlingCms\classes\html\HtmlBlock;
use DarlingCms\classes\html\HtmlContainer;
use DarlingCms\interfaces\html\IHtmlForm;
use DarlingCms\interfaces\html\IHtmlFormElement;

/**
 * Class Form. Defines an implementation of the IHtmlForm interface that extends the HtmlContainer class to generate
 * the html for a form.
 * @package DarlingCms\classes\html\form
 */
class Form extends HtmlContainer implements IHtmlForm
{
    protected $formElements = array();

    /**
     * Form constructor. Sets the Form's method, attributes, and any specified IHtmlFormElement instances.
     * @param string $method The http method the form should use, should be 'get' or 'post'.
     * @param array $attributes Array of attributes to assign to the form.
     * @param IHtmlFormElement ...$formElements IHtmlFormElement instances that should be added to the form on
     *                                          instantiation. Note: Additional IHtmlFormElement instances can be
     *                                          added after instantiation via the addFormElement() method.
     * @see Form::addFormElement()
     */
    public function __construct(string $method, array $attributes = array(), IHtmlFormElement ...$formElements)
    {
        $attributes['method'] = $method;
        parent::__construct(new HtmlBlock(), 'form', $attributes);
        foreach ($formElements as $formElement) {
            $this->addFormElement($formElement);
        }
    }

    /**
     * Add a form element to the form, i.e., an instance of an IHtmlFormElement implementation.
     * @param IHtmlFormElement $formElement The IHtmlFormElement implementation instance to add to the form.
     * @return bool True if element was added, false otherwise.
     */
    public function addFormElement(IHtmlFormElement $formElement): bool
    {
        /**
         * If form element's name contains [], or an element with the same name is not already set, add the
         * form element. @todo Should really check if name ends in [] to prevent matching name like bad[]match
         */
        if (empty(strpos($formElement->getName(), '[]')) === false || !isset($this->formElements[$formElement->getName()]) === true) {
            $this->formElements[$formElement->getName()] = $formElement;
            $this->addHtml($formElement);
            return true;
        }
        return false;
    }

    /**
     * Returns an array of the IHtmlFormElement implementation instances assigned to the form.
     * @return array An array of the IHtmlFormElement implementation instances assigned to the form.
     */
    public function getFormElementsArray(): array
    {
        return $this->formElements;
    }

    /**
     * Returns the name of the http method this form uses.
     * WARNING: If method is set to a value other than 'get' or 'post' the form may not be submittable.
     * @return string The name of the http method this form uses on submission.
     */
    public function getMethod(): string
    {
        return $this->getAttributes()['method'];
    }

}
