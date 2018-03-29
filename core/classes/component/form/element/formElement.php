<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 12:58 AM
 */

namespace DarlingCms\classes\component\form\element;

class formElement extends \DarlingCms\classes\component\html\html
{
    protected $supportedElements = array(
        'button',
        'datalist',
        'keygen',
        'output',
        'select',
        'textarea',
        'input',
        'label'
    );

    public function __construct($elementType, array $elementAttributes = array(), $elementContent = '')
    {
        $this->elementType = $elementType;
        if (in_array($elementType, $this->supportedElements, true) === true) {
            parent::__construct($elementType, $elementContent, $this->unpackAttributes($elementAttributes));
        }
    }

    protected function unpackAttributes(array $elementAttributes)
    {
        $unpackedAttributes = array();
        foreach ($elementAttributes as $attributeName => $elementAttribute) {
            switch (gettype($attributeName)) {
                case 'integer':
                    array_push($unpackedAttributes, $elementAttribute);
                    break;
                case 'string':
                    array_push($unpackedAttributes, "$attributeName=\"$elementAttribute\"");
            }
        }
        return $unpackedAttributes;
    }

    /**
     * Generates the html.
     * @return bool True if html was generated and set and assigned to the html property, false otherwise.
     */
    public function generateHtml()
    {
        $status = parent::generateHtml();
        /* Accommodate the fact that input elements often have content, the parent class does not b/c the
           input tag is a uc tag type... */
        if ($this->tagType === 'input') {
            $this->html .= trim($this->content);
        }
        return $status;
    }

}
