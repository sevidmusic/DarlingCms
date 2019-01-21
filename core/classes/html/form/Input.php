<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 5/25/18
 * Time: 11:05 PM
 */

namespace DarlingCms\classes\html\form;

use DarlingCms\classes\staticClasses\utility\StringUtility;

/**
 * Class Input. Defines an implementation of the HtmlFormElement class that generates an input element.
 * @package DarlingCms\classes\html\form
 */
class Input extends HtmlFormElement
{
    private $showName = false;

    /**
     * Input constructor. Sets the input element's type, name, value, and attributes.
     * @param string $type The input element's type attribute's value.
     * @param string $name The input element's name attribute's value.
     * @param string $value The input element's value attribute's value.
     * @param array $attributes Array of additional attributes to assign to the input element, defaults to an
     *                          empty array.
     * @param  bool $showName If set to true, this will force the input element's name to be shown within the label.
     */
    public function __construct(string $type, string $name, string $value, array $attributes = array(), bool $showName = false)
    {
        $attributes['value'] = $value;
        $attributes['type'] = $type;
        $this->showName = $showName;
        parent::__construct($name, 'input', $attributes, '', true);
    }

    /**
     * Returns the string of html constructed from the tag type, attributes, and content.
     * @return string Returns the string of html constructed from the tag type, attributes, and content.
     */
    public function getHtml(): string
    {
        if ($this->showName === true) {
            return '<label>' . '<span>' . StringUtility::convertFromCamelCase($this->getName()) . ':</span>' . parent::getHtml() . '</label>';
        }
        return '<label>' . parent::getHtml() . '</label>';
    }


}
