<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:08 AM
 */

namespace DarlingCms\classes\component\form\element\input;


class color extends input
{
    public function __construct($name, $value, array $additionalAttributes = array(), $elementContent = '')
    {
        // @todo: Validate $value is hex color string
        parent::__construct('color', $name, $value, $additionalAttributes, $elementContent);
    }

}
