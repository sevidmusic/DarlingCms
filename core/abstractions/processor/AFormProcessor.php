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
 * Class AFormProcessor. Defines an abstract implementation of the IFormProcessor interface
 * that can be extended by objects that are responsible for processing html forms that were
 * built using an IHtmlForm implementation instance.
 * @package DarlingCms\abstractions\processor
 */
abstract class AFormProcessor implements IFormProcessor
{
    /**
     * @var IHtmlForm The IHtmlForm implementation instance that was used to
     *                build the html form processed by this AFormProcessor
     *                implementation instance.
     */
    protected $form;
    /**
     * @var Hidden The Hidden implementation instance that holds the form id used to
     *             identify the form post submission.
     */
    protected $formIdElement;

    /**
     * @var int Will be either the value of INPUT_GET or INPUT_POST depending on the method used by the form.
     * @see INPUT_GET
     * @see INPUT_POST
     */
    protected $method;

    /**
     * @var array Array of processed values, defaults to an empty array.
     * @devNote: It is up to implementations to manage this array.
     */
    protected $processed = array();

    /**
     * FormProcessor constructor. Injects the IHtmlFrom implementation instance that
     * was used to build the html form processed by this AFormProcessor implementation
     * instance.
     * Injects the Hidden implementation instance that holds the form id used to identify
     * the form post submission.
     * @param IHtmlForm $form The IHtmlFrom implementation instance that was used to build
     *                        the html form processed by this AFormProcessor implementation
     *                        instance.
     * @param Hidden $formIdElement The Hidden implementation instance that holds the form
     *                              id used to identify the form post submission.
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
     *
     * @devNote: This method determines whether or not the form was submitted
     * by verifying that the submitted formIdElement's value in 'get' or 'post'
     * matches the actual formIdElement's value.
     *
     * i.e., $_POST[$formIdElement->getName()] === $this->formIdElement->getAttributes()['value']
     *
     * @return bool True if the form was submitted, false otherwise.
     * @see Hidden::getName()
     * @see Hidden::getAttributes()
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
     *
     * @devNote: It is up to implementations to manage this array.
     *
     * @return array An array of the values that were successfully processed, or an empty array
     *               if no values were processed.
     */
    public function getProcessedValues(): array
    {
        return $this->processed;
    }

    /**
     * Returns the IHtmlForm implementation instance that was used to build the html form
     * processed by this AFormProcessor implementation instance.
     *
     * Note: The returned IHtmlForm instance may have been modified by this AFormProcessor
     * implementation instance.
     *
     * @return IHtmlForm The IHtmlForm implementation instance that was used to build the html form
     * processed by this AFormProcessor implementation instance.
     */
    public function getForm(): IHtmlForm
    {
        return $this->form;
    }
}
