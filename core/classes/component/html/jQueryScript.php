<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/15/17
 * Time: 1:54 PM
 */

namespace DarlingCms\classes\component\html;

/**
 * Class jQueryScript. Html component that wraps groups of jQuery component query strings in script tags.
 * @package DarlingCms\classes\component\html
 */
class jQueryScript extends \DarlingCms\classes\component\html
{
    /**
     * jQueryScript constructor. Adds the jQuery components, and calls the parent's construct method
     * specifying that script tags should be used.
     * @param \DarlingCms\abstractions\component\jQuery[] ...$jQuery The jQuery components.
     */
    public function __construct(\DarlingCms\abstractions\component\jQuery ...$jQuery)
    {
        $query = '';
        foreach ($jQuery as $jQ) {
            $query .= $jQ->getQuery();
        }
        parent::__construct('script', $query);
    }
}
