<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 4/2/18
 * Time: 1:45 PM
 */

namespace DarlingCms\interfaces\userInterface;

use DarlingCms\classes\component\html\html;
use DarlingCms\classes\component\html\htmlContainer;

/**
 * Class IuserInterface. This interface defines the basic contract of an object that generates a user interface.
 * @package DarlingCms\interfaces\userInterface
 */
interface IuserInterface
{
    /**
     * Returns the html container for this user interface.
     * @param html ...$html The html objects to append the user interface's htmlContainer.
     * @return htmlContainer Returns the htmlContainer instance responsible for this user interface's html.
     */
    public function getUserInterface(html ...$html): htmlContainer;

}
