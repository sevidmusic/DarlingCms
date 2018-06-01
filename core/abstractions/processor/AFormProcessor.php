<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 6/1/18
 * Time: 4:49 PM
 */

namespace DarlingCms\abstractions\processor;

use DarlingCms\classes\html\form\Hidden;
use DarlingCms\interfaces\html\IHtmlForm;
use DarlingCms\interfaces\processor\IFormProcessor;

/**
 * Class AFormProcessor. Defines an abstract implementation of the IFormProcessor interface.
 * @package DarlingCms\abstractions\processor
 */
abstract class AFormProcessor implements IFormProcessor
{
    /**
     * @var IHtmlForm The injected IHtmlForm implementation instance.
     */
    protected $form;
    /**
     * @var Hidden The injected Hidden object instance that holds the form's id.
     */
    protected $formIdElement;

    /**
     * @var int Will be either the value of INPUT_GET or INPUT_POST depending on the method used by the $form.
     */
    protected $method;

    /**
     * @var array Array of processed values, defaults to an empty array.
     */
    protected $processed = array();

    /**
     * FormProcessor constructor. Injects the IHtmlFrom and Hidden object instances. Determines the method
     * the form uses. Adds the Hidden object instance to the $form as one of the form's IHtmlFormElement
     * implementation instances.
     * @param IHtmlForm $form The IHtmlForm implementation instance this FormProcessor processes.
     * @param Hidden $formIdElement The Hidden object instance that holds the form's id. This object will
     *                              be added to the form's form elements.
     * @see IHtmlForm::getMethod()
     * @see IHtmlForm::addFormElement()
     */
    public function __construct(IHtmlForm $form, Hidden $formIdElement)
    {
        $this->form = $form;
        $this->formIdElement = $formIdElement;
        $this->method = ($this->form->getMethod() === 'get' ? INPUT_GET : INPUT_POST);
        $this->form->addFormElement($formIdElement);
    }

    /**
     * Determines if the form was submitted.
     * @return bool True if the form was submitted, false otherwise.
     */
    public function formSubmitted(): bool
    {
        return (filter_input($this->method, $this->formIdElement->getName()) === $this->formIdElement->getAttributes()['value']);
    }

    /**
     * Returns an array of submitted values, or an empty array if there are no submitted values.
     * @return array An array of submitted values, or an empty array if there are no submitted values.
     */
    public function getSubmittedValues(): array
    {
        return ($this->formSubmitted() === true ? filter_input_array($this->method) : array());
    }

    /**
     * Processes the form.
     * @return bool True if form was processed successfully, false otherwise.
     */
    abstract public function processForm(): bool;

    /**
     * Returns an array of the values that were successfully processed, or an empty array if
     * no values were processed.
     * @return array An array of the values that were successfully processed, or an empty array
     *               if no values were processed.
     */
    public function getProcessedValues(): array
    {
        return $this->processed;
    }

    /**
     * Returns the IHtmlForm implementation instance processed by this FormProcessor instance.
     * Note: The returned IHtmlForm instance may have been modified by this FormProcessor instance.
     * @return IHtmlForm The processed form.
     */
    public function getForm(): IHtmlForm
    {
        return $this->form;
    }
}
