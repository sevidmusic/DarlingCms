<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/16/17
 * Time: 1:17 PM
 */

namespace DarlingCms\classes\component\jQuery;

/**
 * Class anonymousFunction. Defines an implementation of the jQuery block component
 * that creates a block of jQuery that is wrapped in an anonymous function.
 * @package DarlingCms\classes\component\jQuery
 */
class anonymousFunction extends block
{
    /**
     * @inheritdoc
     */
    protected function generateJquery()
    {
        /* Start fresh each time this method is called. */
        unset($this->jQuery);
        $this->jQuery = 'function () {';
        foreach ($this->jQueryObjects as $jQ) {
            $jQuery = trim(strval($jQ));
            $this->jQuery .= $jQuery;
        }
        $this->jQuery .= '}';
        return (isset($this->jQuery) === true);
    }
}
