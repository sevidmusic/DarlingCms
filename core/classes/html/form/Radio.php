<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/26/18
 * Time: 10:03 AM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Radio. Defines an implementation of the Input class that generates a radio input element.
 * @package DarlingCms\classes\html\form
 */
class Radio extends Input
{
    /**
     * Radio constructor. Sets the radio input element's name, value, and attributes.
     * @param string $name The radio input element's name attribute's value.
     * @param string $value The radio input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the radio input element, defaults to an
     *                          empty array.
     */
    public function __construct(string $name, string $value, array $attributes = array())
    {
        parent::__construct('radio', $name, $value, $attributes);
    }

}
