<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:16 AM
 */

namespace DarlingCms\classes\component\form\element;

class button extends formElement
{
    public function __construct(string $label, array $elementAttributes = array())
    {
        parent::__construct('button', $elementAttributes, $label);
    }

}
