<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:06 AM
 */

namespace DarlingCms\classes\component\form\element\input;


class text extends input
{
    public function __construct($name, $value, array $additionalAttributes = array(), $elementContent = '')
    {
        parent::__construct('text', $name, $value, $additionalAttributes, $elementContent);
    }
}
