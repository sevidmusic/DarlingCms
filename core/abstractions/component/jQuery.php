<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/15/17
 * Time: 3:01 PM
 */

namespace DarlingCms\abstractions\component;

/**
 * Class jQuery. Abstract definition of a jQuery component.
 * @package DarlingCms\abstractions\component
 */
abstract class jQuery extends Acomponent
{
    /**
     * @var string The string of jQuery code.
     */
    protected $jQuery = ''; /* Initially set to an empty string, this property must always be a string, or jQuery objects may misbehave. */

    /**
     * @var array Array of jQuery versions this object supports.
     */
    protected $supportedVersions = array('3.2.1');

    /**
     * @var array Array of jQuery actions this object supports.
     */
    protected $supportedActions = array();

    /**
     * @var array Array of jQery events this object supports.
     */
    protected $supportedEvents = array();

    /**
     * jQuery constructor. Calls the parent Acomponent class's __construct method.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the component name.
     * @param string $name The name to use.
     * @return bool True if component name was set, false otherwise.
     */
    public function setComponentName(string $name)
    {
        $this->componentName = $name;
        return (isset($this->componentName) && $this->componentName === $name);
    }

    /**
     * Set the component attributes.
     * @param array $attributes The component attributes.
     */
    public function setComponentAttributes(array $attributes)
    {
        $this->componentAttributes = $attributes;
        return (isset($this->componentAttributes) && $this->componentAttributes === $attributes);
    }

    /**
     * Calls getQuery() to obtain the generated jQuery.
     * @return string The jQuery.
     */
    function __toString()
    {
        return $this->getQuery();
    }

    /**
     * Calls generateJquery() and then returns the generated jQuery.
     * @return string The generated jQuery, or an empty string.
     */
    public function getQuery()
    {
        /* Re-generate jQuery whenever it is requested to ensure it is up to date with any changes that
           may have occurred since last call to getQuery(). */
        $this->generateJquery();
        /*
         * Return jQuery so long as it is a string, if it is not a string it is corrupted and an empty
         * string should be returned instead. This will ensure that this objects __toString() method
         * returns a string when as it calls this method to obtain it's return value.
         */
        return (is_string($this->jQuery) ? $this->jQuery : '');
    }

    /**
     * Generates the component's jQuery and assigns it to the $jQuery property.
     * @return bool True if jQuery was generated and stored in the $jQuery property, false otherwise.
     */
    abstract protected function generateJquery();

    /**
     * Register a version of jQuery supported by this jQuery object.
     * @param string $version The version to register. e.g., 3.2.1
     * @return bool True if version was registered, false otherwise.
     */
    protected function registerVersion(string $version)
    {
        $count = count($this->supportedVersions);
        return (array_push($this->supportedVersions, trim($version)) > $count);
    }

    /**
     * Register an action supported by this jQuery object.
     * @param string $action The action to register.
     * @return bool True if action was registered, false otherwise.
     */
    protected function registerAction(string $action)
    {
        $count = count($this->supportedActions);
        return (array_push($this->supportedActions, trim($action)) > $count);
    }

    /**
     * Register an event supported by this jQuery object.
     * @param string $event The event to register.
     * @return bool True if event was registered, false otherwise.
     */
    protected function registerEvent(string $event)
    {
        $count = count($this->supportedEvents);
        return (array_push($this->supportedEvents, trim($event)) > $count);
    }

}
