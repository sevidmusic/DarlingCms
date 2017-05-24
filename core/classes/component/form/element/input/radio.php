<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:07 AM
 */

namespace DarlingCms\classes\component\form\element\input;


class radio extends input
{
    public function __construct($name, $value, array $additionalAttributes = array(), $elementContent = '')
    {
        parent::__construct('radio', $name, $value, $additionalAttributes, $elementContent);
    }

}
