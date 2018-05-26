<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 11:35 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Text. Defines an implementation of the Input class that generates a text input element.
 * @package DarlingCms\classes\html\form
 */
class Text extends Input
{
    /**
     * Text constructor. Sets the text input element's name, value, and attributes.
     * @param string $name The text input element's name attribute's value.
     * @param string $value The text input element's value attribute's value. Defaults to an empty string.
     * @param array $attributes Array of additional attributes to assign to the text input element, defaults to an
     *                          empty array.
     */
    public function __construct(string $name, string $value = '', array $attributes = array())
    {
        parent::__construct('text', $name, $value, $attributes);
    }
}
