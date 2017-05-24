<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:06 AM
 */

namespace DarlingCms\classes\component\form\element\input;

class input extends \DarlingCms\classes\component\form\element\formElement
{
    public function __construct(string $type, string $name, string $value, array $additionalAttributes = array(), $elementContent = '')
    {
        $attributes = array(
            'type' => $type,
            'name' => $name,
            'value' => $value,
        );

        if (!empty($additionalAttributes) === true) {
            foreach ($additionalAttributes as $attribute) {
                array_push($attributes, $attribute);
            }
        }
        parent::__construct('input', $attributes, $elementContent);
    }

}
