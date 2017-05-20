<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/15/17
 * Time: 12:52 PM
 */

namespace DarlingCms\classes\component\jQuery;

/**
 * Class action.
 * Note: The following actions are supported:
 *  - hide
 *  - show
 *  - resizable
 *  - click
 *  - css
 *  - slideToggle
 *  - prop
 * Support for other actions may be added in the future...
 * @package DarlingCms\classes\component\jQuery
 */
class action extends \DarlingCms\abstractions\component\jQuery
{
    /**
     * @var string $selector The selector.
     */
    private $selector;

    /**
     * @var string $action The action
     */
    private $action;

    /**
     * action constructor. Calls the parent's constructor, register's the actions supported by this object,
     * set's the selector, the action, the component name, and the component attributes.
     * @param string $selector The selector.
     * @param string $action The action.
     * @param array $params Array of parameters that should be passed to the action.
     */
    public function __construct(string $selector, string $action, array $params = array())
    {
        parent::__construct();
        /* Register the actions this object supports */
        $this->registerActions(array('hide', 'show', 'resizable', 'click', 'css', 'slideToggle', 'prop'));
        /* Set initial selector*/
        $this->setSelector($selector);
        /* Set the initial action */
        $this->setAction($action);
        /* Set component initial name */
        $this->setComponentName(trim($selector) . ucfirst(trim($action)));
        /* Set params array as the component attributes. */
        $this->setComponentAttributes($params);
    }

    /**
     * Register actions supported by this implementation.
     * @param array $actions Array of actions to register.
     * @return bool True if all actions were registered successfully, false otherwise.
     */
    private function registerActions(array $actions)
    {
        $status = array();
        foreach ($actions as $action) {
            $status[] = $this->registerAction($action);
        }
        return in_array(false, $status, true);
    }

    /**
     * Set the selector.
     * @param string $selector The selector.
     * @return bool True if selector was set, false otherwise.
     */
    public function setSelector(string $selector)
    {
        $this->selector = trim($selector);
        $this->generateJquery(); // re-generate jQuery whenever selector is changed.
        $this->setComponentName(trim($this->selector) . ucfirst(trim($this->action))); // reset name whenever selector is changed
        return (isset($this->selector) && $this->selector === $selector);
    }

    /**
     * @inheritdoc
     */
    protected function generateJquery()
    {
        if (isset($this->action) === true) {
            $this->jQuery = "\$( \"{$this->selector}\" ).{$this->action}(" . $this->unpackParams() . ");";
        }
        return (isset($this->jQuery));
    }

    /**
     * Generates the parameter string for the action from the component attributes array.
     * @return string The parameter string.
     */
    protected function unpackParams()
    {
        $paramString = '';
        $params = $this->getComponentAttributes();
        if (is_array($params)) {
            $paramCount = count($params);
            $counter = 1;
            foreach ($params as $param) {
                switch (is_array($param)) {
                    case true:
                        // handle parameter array : should output something like -> { param1: value, param2: value, ... }
                        $filter1 = str_replace('":"', ': ', json_encode($param));
                        $paramString .= str_replace('"', '', $filter1);
                        break;
                    default:
                        $paramString .= $param . (($paramCount) > 1 && ($counter !== ($paramCount)) ? ',' : '');
                        break;
                }
                $counter++;
            }
        }
        return $paramString;
    }

    /**
     * Set the action.
     * @param string $action The selector.
     * @return bool True if action was set, false otherwise.
     */
    public function setAction(string $action)
    {
        if (in_array($action, $this->supportedActions)) {
            $this->action = trim($action);
            $this->generateJquery(); // re-generate jQuery whenever action is changed.
            $this->setComponentName(trim($this->selector) . ucfirst(trim($this->action))); // reset name whenever action is changed
        }
        return (isset($this->action) && $this->action === $action);
    }

}
