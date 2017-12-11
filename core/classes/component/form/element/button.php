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
    /**
     * button constructor.
     * @param string $label Button text.
     * @param string $name String to identify $_GET or $_POST value.
     * @param string $value $_GET or $_POST value.
     * @param array $additionalAttributes Array of additional html attributes to assign to button.
     */
    public function __construct(string $label, string $name, string $value, array $additionalAttributes = array())
    {
        $additionalAttributes['name'] = $name;
        $additionalAttributes['value'] = $value;
        parent::__construct('button', $additionalAttributes, $label);
    }

}
