<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 11:55 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Hidden. Defines an implementation of the Input class that generates a hidden input element.
 * @package DarlingCms\classes\html\form
 */
class Hidden extends Input
{
    /**
     * Hidden constructor. Sets the hidden input element's name, and value.
     * @param string $name The hidden input element's name attribute's value.
     * @param string $value The hidden input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the hidden input element.
     */
    public function __construct(string $name, string $value, $attributes = array())
    {
        parent::__construct('hidden', $name, $value, $attributes);
    }
}
