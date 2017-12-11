<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:16 AM
 */

namespace DarlingCms\classes\component\form\element;


class textarea extends formElement
{
    public function __construct(string $name, string $initialText = '', array $elementAttributes = array())
    {
        $elementAttributes['name'] = $name;
        parent::__construct('textarea', $elementAttributes, $initialText);
    }
}
