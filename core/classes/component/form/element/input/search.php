<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:13 AM
 */

namespace DarlingCms\classes\component\form\element\input;


class search extends input
{
    public function __construct($name, $value, array $additionalAttributes = array(), $elementContent = '')
    {
        parent::__construct('search', $name, $value, $additionalAttributes, $elementContent);
    }

}