<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 12/11/17
 * Time: 2:59 PM
 */

namespace DarlingCms\classes\accessControl;

/**
 * Class privilege. Defines a named privilege and the actions associated with it. Privileges basically can
 * be used to define access configurations, for example, a privilege called "Manage Content" could be
 * defined with the following actions: create, read, update, and delete. Then anytime access to managing
 * content needed to be controlled a check for the "Manage Content" privilege could be done before allowing
 * content to be managed.
 * @package DarlingCms\classes\accessControl
 */
class privilege
{
    /**
     * @var string The name of this privilege instance.
     */
    private $privilegeName;
    /**
     * @var array Array of actions associated with this privilege.
     */
    private $actions = array();

    /**
     * privilege constructor.
     * @param string $privilegeName A name to use for this privilege.
     */
    public function __construct(string $privilegeName)
    {
        $this->privilegeName = $privilegeName;
    }

    /**
     * Add an action to this privilege.
     * @param string $action The action to add to the privilege. (Note: actions are just descriptive strings,
     * it is advised that they be kept short and to the point. For example: "read", or "view content" would be
     * good concise action strings, whereas, "read and create content but not update", though valid, would not
     * be such a good action string.)
     * @return bool True if action was added successfully, false otherwise.
     */
    public function addAction(string $action): bool
    {
        /* If action does not already exist, add it. */
        if ($this->hasAction($action) === false) {
            array_push($this->actions, $action);
            return $this->hasAction($action);
        }
        return false;
    }

    /**
     * Check if a specified action is associated with this privilege.
     * @param string $action The action to check for.
     * (Note: Actions are case sensitive strings, i.e "read" and "Read" are considered different)
     * @return bool True if this privilege has the specified action, otherwise false.
     */
    public function hasAction(string $action): bool
    {
        return in_array($action, $this->actions, true);
    }

    /**
     * Remove an action associated with this privilege.
     * @param string $action The action to remove.
     * (Note: Actions are case sensitive, i.e "read" and "Read" are considered different)
     * @return bool True if action was removed successfully, false otherwise.
     */
    public function removeAction(string $action): bool
    {
        /* Loop through actions array till target action is found. */
        foreach ($this->actions as $index => $target) {
            if ($target === $action) {
                /* Remove the target action. */
                unset($this->actions[$index]);
            }
        }
        /* Re-index the actions array. */
        sort($this->actions);
        return !$this->hasAction($action);
    }

    /**
     * Returns the privilege's name.
     * @return string The name of the privilege.
     */
    public function getPrivilegeName(): string
    {
        return $this->privilegeName;
    }

    /**
     * Returns the array of actions associated with this privilege.
     * @return array The actions array.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

}
