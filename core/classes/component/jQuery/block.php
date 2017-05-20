<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/15/17
 * Time: 4:09 PM
 */

namespace DarlingCms\classes\component\jQuery;

/**
 * Class block. Implementation of a jQuery component uses a group of jQuery components to create
 * a jQuery document ready event.
 * @todo: Possibly abstract further making the block class abstract and rename this class to jQReadyEvent,
 *        and have this class and the anonymousFunction class just be implementations of block, instead of
 *        anonymousFunction implementing this concrete block... This would make each class more independent,
 *        and would involve accommodating logic that specified the block opening and closing strings in the
 *        abstract class.
 *
 *        new generateJquery() logic to be placed in abstract class: "$oString . (implode: $jQueryObjects) . $cString"
 *        need to define new methods setOpeningString() and setClosingString();
 *
 * @package DarlingCms\classes\component\jQuery
 */
class block extends \DarlingCms\abstractions\component\jQuery
{
    /**
     * @var array Array of jQuery objects.
     */
    protected $jQueryObjects = array();

    /**
     * block constructor. Adds the specified jQuery components and generates the jquery.
     * @param \DarlingCms\abstractions\component\jQuery[] ...$jQuery The jQuery objects to use in the jQuery event.
     * @return bool True if query components were added and jQuery was generated, false otherwise.
     */
    public function __construct(\DarlingCms\abstractions\component\jQuery ...$jQuery)
    {
        parent::__construct();
        foreach ($jQuery as $jQ) {
            array_push($this->jQueryObjects, $jQ);
        }
        return $this->generateJquery();
    }

    /**
     * @inheritdoc
     */
    protected function generateJquery()
    {
        /* Start fresh each time this method is called. */
        unset($this->jQuery);
        $this->jQuery = 'jQuery(function ($) {';
        foreach ($this->jQueryObjects as $jQ) {
            $jQuery = strval($jQ);
            $this->jQuery .= $jQuery;
        }
        $this->jQuery .= '});';
        return (isset($this->jQuery) === true);
    }

    /**
     * Add a jQuery object to the block.
     *
     * Note: This method will call generateJquery() after the new jQuery component is added to
     * ensure the jQuery is up to date.
     *
     * @param \DarlingCms\abstractions\component\jQuery $jQuery The jQuery component to add.
     * @return bool True if query was added and jQuery was re-generated, false otherwise.
     */
    public function addQuery(\DarlingCms\abstractions\component\jQuery $jQuery)
    {
        /* Add jQuery component. */
        array_push($this->jQueryObjects, $jQuery);
        /* Re-generate jQuery each time a new query is added. */
        return $this->generateJquery();
    }


}
