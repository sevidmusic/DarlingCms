<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 10:17 PM
 */

namespace DarlingCms\classes\html\form;

/**
 * Class TextArea. Defines an implementation of the HtmlFormElement class that generates a textarea element.
 * @package DarlingCms\classes\html\form
 */
class TextArea extends HtmlFormElement
{
    /**
     * TextArea constructor. Sets the elements name attribute, attributes, and content.
     * @param string $name The value of the elements name attribute.
     * @param array $attributes Array of attributes to assign to the element, defaults to an empty array.
     * @param string $content The textarea's initial text. Defaults to an empty string.
     */
    public function __construct(string $name, array $attributes = array(), string $content = '')
    {
        parent::__construct($name, 'textarea', $attributes, $content, false);
    }

}
