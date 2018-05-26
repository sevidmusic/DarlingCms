<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/26/18
 * Time: 10:18 AM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Checkbox. Defines an implementation of the Input class that generates a checkbox input element.
 * @package DarlingCms\classes\html\form
 */
class Checkbox extends Input
{
    /**
     * Checkbox constructor. Sets the checkbox input element's name, value, and attributes.
     * @param string $name The checkbox input element's name attribute's value.
     * @param string $value The checkbox input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the checkbox input element, defaults
     *                          to an empty array.
     */
    public function __construct(string $name, string $value, array $attributes = array())
    {
        parent::__construct('checkbox', $name, $value, $attributes);
    }

}
