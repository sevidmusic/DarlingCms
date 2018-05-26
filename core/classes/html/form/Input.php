<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 11:05 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Input. Defines an implementation of the HtmlFormElement class that generates an input element.
 * @package DarlingCms\classes\html\form
 */
class Input extends HtmlFormElement
{
    /**
     * Input constructor. Sets the input element's type, name, value, and attributes.
     * @param string $type The input element's type attribute's value.
     * @param string $name The input element's name attribute's value.
     * @param string $value The input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the input element, defaults to an
     *                          empty array.
     */
    public function __construct(string $type, string $name, string $value, array $attributes = array())
    {
        $attributes['value'] = $value;
        $attributes['type'] = $type;
        parent::__construct($name, 'input', $attributes, '', true);
    }
}
