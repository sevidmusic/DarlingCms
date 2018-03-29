<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/28/18
 * Time: 7:17 PM
 */

namespace DarlingCms\classes\component\form\element;


class label extends formElement
{
    /**
     * label constructor.
     * @param string $for The id of the form element this label is assigned to.
     * @param string $label The string to use for the label.
     * @param array $additionalAttributes Any additional attributes that should be assigned to the label.
     */
    public function __construct(string $for, string $label, array $additionalAttributes = array())
    {
        parent::__construct('label', array_merge(array('for' => $for), $additionalAttributes), $label);
    }
}
