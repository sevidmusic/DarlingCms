<?php
/**
 * Created by Sevi Donnely Foreman.
 * Date: 5/25/18
 * Time: 10:28 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class Select. Defines an implementation of the HtmlFormElement class that generates a select element.
 * @package DarlingCms\classes\html\form
 */
class Select extends HtmlFormElement
{
    /**
     * Select constructor. Sets the elements name attribute, options, and attributes.
     * @param string $name The value of the select element's name attribute.
     * @param array $options Array of options for the select element. Defaults to an empty array.
     * @param array $attributes Array of attributes to assign to the select element, defaults to an empty array.
     */
    public function __construct(string $name, array $options = array(), array $attributes = array())
    {
        parent::__construct($name, 'select', $attributes, $this->generateOptionsHtml($options), false);
    }

    /**
     * Generates the html for the select element's options.
     * @param array $options Array of options for the select element.
     * @return string The html for the select element's options
     */
    private function generateOptionsHtml(array $options): string
    {
        $optionString = '';
        foreach ($options as $option) {
            $optionString .= '<option>' . $option . '</option>';
        }
        return $optionString;
    }
}
