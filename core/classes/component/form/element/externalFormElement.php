<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 12:58 AM
 */

namespace DarlingCms\classes\component\form\element;

class externalFormElement extends formElement
{
    /**
     * externalFormElement constructor.
     * @param formElement $formElement Instance of a \DarlingCms\classes\component\form\element\formElement object.
     * @param string $parentForm The id of the form this external form element is belongs to.
     */
    public function __construct(formElement $formElement, string $parentForm)
    {
        $attributes = $formElement->getComponentAttributes();
        array_push($attributes, "form=\"$parentForm\"");
        parent::__construct($formElement->tagType, $attributes, $formElement->content);
    }

}
