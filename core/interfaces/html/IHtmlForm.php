<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/23/18
 * Time: 6:56 AM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlForm. Defines the basic contract of an interface that implements the IHtmlContainer interface
 * to generate the html for an html form.
 * @package DarlingCms\interfaces\html
 */
interface IHtmlForm extends IHtmlContainer
{
    /**
     * Adds an IHtml implementation instance to the form.
     * @param IHtml $html The IHtml implementation instance to add to the form.
     * @return bool Returns true if the IHtml implementation instance was added to the form, false otherwise.
     */
    public function addHtml(IHtml $html): bool;

    /**
     * Returns an array of the IHtml implementation instances assigned to the form, or an empty array
     * if the form is not assigned any IHtml implementation instances.
     * @return array An array of the IHtml implementations assigned to the form.
     */
    public function getHtmlArray(): array;

    /**
     * Returns the tag type of the form.
     * WARNING: Implementations that do not return the string 'form' will not generate an Html compliant form.
     * @return string The tag type of the form.
     */
    public function getTagType(): string;

    /**
     * Returns an array of attribute name/value pairs assigned to the form, i.e., the attributes
     * assigned to the form tag, or an empty array if the form is not assigned any attributes.
     * @return array An array of attribute name/value pairs assigned to the form, or an empty
     *               array if the form is not assigned any attributes.
     */
    public function getAttributes(): array;

    /**
     * Returns the html string constructed from the IHtml and IHtmlFormElement implementation instances assigned
     * to the form, or an empty string if the form is not assigned any IHtml or IHtmlFormElement implementation
     * instances.
     * @return string The html string constructed from the IHtml and IHtmlFormElement implementation instances
     *                assigned to the form, or an empty string if the form is not assigned any IHtml or
     *                IHtmlFormElement implementation instances.
     */
    public function getContent(): string;

    /**
     * Returns the forms html.
     * @return string The forms html.
     */
    public function getHtml(): string;

    /**
     * Add a form element to the form.
     * @param IHtmlFormElement $formElement The IHtmlFormElement instance to add to the form.
     * @return bool True if element was added, false otherwise.
     */
    public function addFormElement(IHtmlFormElement $formElement): bool;

    /**
     * Returns an array of the IHtmlFormElement instances assigned to the form.
     * @return array An array of the IHtmlFormElement instances assigned to the form.
     */
    public function getFormElementsArray(): array;

    /**
     * Returns the name of the http method this form uses upon submission.
     * Note: Implementations should either return the string 'get', or return the string 'post'.
     * WARNING: Implementations that return a string other than 'get' or 'post' may not be submittable.
     * @return string The name of the http method this form uses to submit it's form element values.
     */
    public function getMethod(): string;
}

