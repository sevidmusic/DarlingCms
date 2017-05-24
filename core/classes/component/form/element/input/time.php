<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:14 AM
 */

namespace DarlingCms\classes\component\form\element\input;


class time extends input
{
    public function __construct($name, $value, array $additionalAttributes = array(), $elementContent = '')
    {
        parent::__construct('time', $name, $value, $additionalAttributes, $elementContent);
    }

}
