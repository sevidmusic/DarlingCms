<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 5/24/18
 * Time: 11:58 PM
 */

namespace DarlingCms\classes\html;

use DarlingCms\interfaces\html\IHtmlFormElement;

/**
 * Class HtmlFormElement. Defines an extension of the HtmlTag class that implements the IHtmlFormElement interface
 * which can be used to generate html for a variety of form elements.
 * @package DarlingCms\classes\html
 */
class HtmlFormElement extends HtmlTag implements IHtmlFormElement
{
    /**
     * HtmlFormElement constructor. Sets the elements name attribute, tag type, attributes, content, and whether
     * or not to exclude to elements closing tag.
     * @param string $name The value of the elements name attribute.
     * @param string $tagType The elements tag type.
     * @param array $attributes Array of attributes to assign to the element, defaults to an empty array.
     * @param string $content The elements content, i.e., a string to insert between the element's tags,
     *                        defaults to an empty string.
     * @param bool $excludeClosingTag Whether or to exclude the closing tag, defaults to false.
     */
    public function __construct(string $name, string $tagType, array $attributes = array(), string $content = '', bool $excludeClosingTag = false)
    {
        $attributes['name'] = $name;
        parent::__construct($tagType, $attributes, $content, $excludeClosingTag);
    }

    /**
     * Returns the value of the form elements name attribute.
     * @return string The value of the form elements name attribute.
     */
    public function getName(): string
    {
        return $this->getAttributes()['name'];
    }

}
