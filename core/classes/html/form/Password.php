<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 11:47 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Password. Defines an implementation of the Input class that generates a password input element.
 * @package DarlingCms\classes\html\form
 */
class Password extends Input
{
    /**
     * Password constructor. Sets the password input element's name, value, and attributes.
     * @param string $name The password input element's name attribute's value.
     * @param string $value The password input element's value attribute's value. Defaults to an empty string.
     * @param array $attributes Array of additional attributes to assign to the password input element, defaults to an
     *                          empty array.
     */
    public function __construct(string $name, string $value = '', array $attributes = array())
    {
        parent::__construct('password', $name, $value, $attributes);
    }
}
