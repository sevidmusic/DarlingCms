<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/18/17
 * Time: 3:45 PM
 */

namespace DarlingCms\classes\component\jQuery;

/**
 * Class eventListener. A jQuery component that constructs a jQuery event listener from a group of jQuery actions.
 * @package DarlingCms\classes\component\jQuery
 */
class eventListener extends \DarlingCms\abstractions\component\jQuery
{
    /**
     * @var action The constructed event listener.
     */
    private $eListener;

    /**
     * eventListener constructor. Calls the parent's constructor and constructs the event listener.
     * @param string $listener The selector that should listen for the event.
     * @param string $event The event to listen for.
     * @param action[] ...$actions The jQuery actions to trigger when the event occurs.
     */
    public function __construct(string $listener, string $event, \DarlingCms\classes\component\jQuery\action ...$actions)
    {
        parent::__construct();
        $func = new \DarlingCms\classes\component\jQuery\anonymousFunction();
        foreach ($actions as $action) {
            $func->addQuery($action);
        }
        $this->eListener = new \DarlingCms\classes\component\jQuery\action($listener, $event, array($func));
    }

    /**
     * @inheritdoc
     */
    protected function generateJquery()
    {
        /* Assign the eListener action's jQuery to the jQuery property. */
        $this->jQuery = $this->eListener->getQuery();
        return ((isset($this->jQuery) === true) && ($this->jQuery === $this->eListener->jQuery));
    }

}
