<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/23/18
 * Time: 6:59 AM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlFormElement. Defines the basic contract of an implementation of the IHtmlTag interface
 * that generates the html for a form element.
 * @package DarlingCms\interfaces\html
 */
interface IHtmlFormElement extends IHtmlTag
{
    /**
     * Returns the form element's tag type.
     * @return string The form element's tag type.
     */
    public function getTagType(): string;

    /**
     * Returns an array of attribute name/value pairs assigned to the form element, where names are keys and
     * values are values. Note: This method will return an empty array if the form element is not assigned
     * any attributes.
     * @return array An array of attributes assigned to the form element.
     */
    public function getAttributes(): array;

    /**
     * Returns the form element's content, or an empty string if the form element does not have any content.
     * @return string The form element's content, or an empty string if the form element does not have any content.
     */
    public function getContent(): string;

    /**
     * Returns the form element's html.
     * @return string The form element's html.
     */
    public function getHtml(): string;

    /**
     * Returns the value of the form elements name attribute.
     * @return string The value of the form elements name attribute.
     */
    public function getName(): string;
}
