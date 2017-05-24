<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/17
 * Time: 1:00 AM
 */

namespace DarlingCms\classes\component\form\formatter;


class formElementWrapper extends \DarlingCms\classes\component\form\element\formElement
{
    public function __construct($tagType, array $attributes = array(), \DarlingCms\classes\component\form\element\formElement ...$formElements)
    {
        /**
         * Note: This object is considered a formElement component even though it behaves like an html component
         * because it's responsibility is grouping form element together, and it therefore needs to be qualified
         * to be added to a form component.
         */
        \DarlingCms\classes\component\html\html::__construct($tagType, implode(PHP_EOL, $formElements), $attributes);
    }

}
