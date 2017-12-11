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
    public function __construct(string $name, array $selectOptions, array $additionalAttributes = array())
    {
        /* Set name attribute to $name. */
        $additionalAttributes['name'] = $name;
        /* Initialize options array. */
        $options = array();
        /* Create <option> tags foreach option. */
        foreach ($selectOptions as $option) {
            if (is_string($option) === true) {
                array_push($options, '<option>' . $option . '</option>');
            }
        }
        parent::__construct('select', $additionalAttributes, implode(PHP_EOL, $options));
    }

}
