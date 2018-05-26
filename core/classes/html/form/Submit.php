<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/26/18
 * Time: 12:03 AM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Submit. Defines an implementation of the Input class that generates a submit input element.
 * @package DarlingCms\classes\html\form
 */
class Submit extends Input
{
    /**
     * Submit constructor. Sets the submit input element's name, value, and attributes.
     * @param string $name The submit input element's name attribute's value.
     * @param string $value The submit input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the submit input element, defaults to an
     *                          empty array.
     */
    public function __construct(string $name, string $value, $attributes = array())
    {
        parent::__construct('submit', $name, $value, $attributes);
    }
}
