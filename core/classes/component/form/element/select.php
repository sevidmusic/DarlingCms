<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:16 AM
 */

namespace DarlingCms\classes\component\form\element;

class select extends formElement
{
    // @todo: $options should actually require the option implementation of the html component object.
    public function __construct(array $elementAttributes = array(), \DarlingCms\classes\component\html\html ...$options)
    {
        parent::__construct('select', $elementAttributes, implode(PHP_EOL, $options));
    }

}
