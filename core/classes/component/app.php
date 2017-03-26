<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/20/17
 * Time: 5:08 PM
 */

namespace DarlingCms\classes\component;

/**
 * Class app. Defines an implementation of the  \DarlingCms\abstractions\component\Acomponent class
 * that represents a Darling CMS app component.
 *
 * @package DarlingCms\classes\component
 */
class app extends \DarlingCms\abstractions\component\Acomponent
{
    /**
     * app constructor. Calls the parents __construct() method to set the componentId and
     * componentType, then sets the componentName, and initializes the component attributes array.
     *
     * @param string $name The name of the app.
     *
     * @param array $customAttributes (optional) An array of custom attributes for the app. Defaults to
     *                                           an empty array.
     */
    final public function __construct(string $name, array $customAttributes = array())
    {
        /* Call \DarlingCms\abstractions\component\Acomponent __construct() method
           to set the componentId and componentType. */
        parent::__construct();
        /* Set the componentName using the $name parameter. */
        $this->setComponentName($name);
        /* Create an array to be used as the initial componentAttributes array whose
           structure accommodates the expected attributes of an app component. */
        $attributes = array(
            // Disable app by default.
            'enabled' => false,
            // Initialize dependencies array, should be empty by default.
            'dependencies' => array(),
            // Initialize themes array, should be empty by default.
            'themes' => array(),
            // Initialize customAttributes array using the $customAttributes parameter, may be empty.
            'customAttributes' => $customAttributes,
        );
        /* Set componentAttributes array. */
        $this->setComponentAttributes($attributes);
    }

    /**
     * Sets the componentName.
     *
     * Note: The componentName is set upon instantiation. Calling this method outside of the __construct()
     * method will have no effect. This method is only public because it must honor the interface defined by
     * the \DarlingCms\abstractions\component\Acomponent abstract class.
     *
     * @param string $name Should be the name of the app, this value will be assigned as the componentName.
     *
     * @return bool Returns true if componentName was set successfully, false otherwise.
     * Note: This method will return false if called from outside of the constructor.
     *
     */
    public function setComponentName(string $name)
    {
        /* Check if componentName is already initialized. */
        if ($this->propertyInitialized('componentName') === true) {
            /* Return false if componentName is already initialized. */
            return false;
        }
        /* Assign $name to componentName */
        $this->componentName = $name;
        /* Return true if $name was assigned to componentName successfully, false otherwise. */
        return ($this->componentName === $name);
    }

    /**
     * Determines if a property has been initialized. This method can also be used to
     * check if an attribute has been initialized in the componentAttributes array by
     * setting the $checkAttribute parameter to true, and specifying the $attributeKey
     * of the attribute to check.
     *
     * @param string $property The name of the property being checked.
     *
     * @param bool $checkAttribute If set to true, the value specified by the $attributeKey will be
     *                             searched for in the componentAttributes array. If set to false,
     *                             the specified $property will be checked. Defaults to false.
     *
     *
     * @return bool True if specified $property has been initialized, false otherwise.
     * Note: If $checkAttribute is set to true then this method will return true if the specified $attributeKey
     * has been initialized in the componentAttributes array, false otherwise.
     */
    final private function propertyInitialized(string $property, bool $checkAttribute = false, string $attributeKey = '')
    {
        /* If $checkAttribute parameter is set to true then check the componentAttributes array. */
        if ($checkAttribute === true) {
            /* Return true if specified componentAttribute is set, false otherwise. */
            return isset($this->componentAttributes[$attributeKey]);
        }
        /* Return true if $property is set, false otherwise.*/
        return isset($this->$property);
    }

    /**
     * Sets the componentAttributes array.
     *
     * Warning: To prevent potentially corrupting the expected structure of this implementation's
     * componentAttributes array, which is setup by the constructor upon instantiation, this
     * method will have no effect if called from outside of the constructor, even though it
     * is a public method.
     *
     * Note: This method is only public because it must honor the interface defined by
     * the \DarlingCms\abstractions\component\Acomponent abstract class. However, in this
     * implementation it will have no effect if called from outside the __construct()
     * method. To modify the componentAttributes array after instantiation use the
     * enableApp(), disableApp(), registerDependency(), and setCustomAttribute() methods.
     *
     * @param array $attributes Array of attributes to be assigned to the componentAttributes array.
     *
     * @return bool True if the component attributes array was set successfully, false otherwise.
     * Note: This method will return false if called from outside of the constructor.
     *
     */
    public function setComponentAttributes(array $attributes)
    {
        /* Protect against overwriting the componentAttributes array if it has already been initialized. */
        if ($this->propertyInitialized('componentAttributes') === true) {
            /* Return false if attempt is made to set the componentAttributes array after it has already been initialized. */
            return false;
        }
        /* Assign the $attributes array to the componentAttributes property. */
        $this->componentAttributes = $attributes;
        /* Return true if the componentAttributes array was set, false otherwise. */
        return ($this->componentAttributes === $attributes);
    }

    /**
     * Gets a specified componentAttribute value.
     *
     * @param string $attributeName The name of the component attribute whose value should be returned.
     *
     * @return mixed|bool Returns the specified componentAttribute's value, or false on failure.
     */
    public function getComponentAttributeValue(string $attributeName)
    {
        /* Make sure the specified attribute exists. */
        if (isset($this->componentAttributes[$attributeName]) === true) {
            /* Return the specified attribute's value. */
            return $this->componentAttributes[$attributeName];
        }
        /* Return false if specified attribute does not exist. */
        return false;
    }

    /**
     * Sets the enabled componentAttribute to true.
     *
     * @return bool True if the enabled componentAttribute was set to true, false otherwise.
     */
    public function enableApp()
    {
        return $this->setComponentAttribute('enabled', true);
    }

    /**
     * Set the value of a specified componentAttribute.
     *
     * @param string $attributeName The name of the componentAttribute whose value should be set.
     *                              Options: dependencies, themes, customAttributes, or enabled.
     *                              Note: Passing an attribute name that is not one of the options
     *                              listed will have no effect.
     *
     * @param mixed $attributeValue The value to set for the componentAttribute.
     *
     * @param string $customAttributeKey (optional) A custom key to use when setting a value in the
     *                                              componentAttributes array's customAttributes array.
     *                                              Only used when setting a value in the customAttributes
     *                                              array.
     *
     * @return bool Returns true if componentAttribute was set successfully, false otherwise.
     *
     * Note: This method will return false if the $attributeName is not one of the following options:
     *       dependencies, themes, customAttributes, or enabled.
     *
     * Note: This method will return false if the $attributeName does not exist in the componentAttributes
     *       array.
     */
    private function setComponentAttribute(string $attributeName, $attributeValue, string $customAttributeKey = '')
    {
        /* Make sure the attribute being set exists in the componentAttributes array. */
        if (in_array($attributeName, array_keys($this->componentAttributes), true) === true) {
            /* Handle setting of attribute differently depending on which attribute is being set. */
            switch ($attributeName) {
                case 'dependencies':
                case 'themes':
                    /* Add the $attributeValue to the specified componentAttributes[$attributeName] array. */
                    array_push($this->componentAttributes[$attributeName], $attributeValue);
                    /* Return true if $attributeValue was added to the specified componentAttributes[$attributeName] array, false otherwise. */
                    return in_array($attributeValue, $this->componentAttributes[$attributeName], true);
                case 'customAttributes':
                    /* Make sure $customAttributeKey was specified. */
                    if (isset($customAttributeKey) && $customAttributeKey !== '') {
                        /* Add $attributeValue to the specified componentAttributes[$attributeName][$customAttributeKey] array. */
                        $this->componentAttributes[$attributeName][$customAttributeKey] = $attributeValue;
                        /* Return true if $attributeValue was added to the specified componentAttributes[$attributeName][$customAttributeKey]
                           array, false otherwise. */
                        return ($this->componentAttributes[$attributeName][$customAttributeKey] === $attributeValue);
                    }
                    /* Log error $customAttributeKey is not specified. */
                    error_log('Attempt to set customAttribute for ' . $this->getComponentName() . ' without specifying customAttributeKey for component type ' . $this->getComponentType() . ' with component id ' . $this->getComponentId());
                    /* Return false if $customAttributeKey is not specified. */
                    return false;
                case 'enabled':
                    /* Set componentAttributes[$attributeName] to $attributeValue. */
                    $this->componentAttributes[$attributeName] = $attributeValue;
                    /* Return true if componentAttributes[$attributeName] wsa set to $attributeValue, false otherwise. */
                    return ($this->componentAttributes[$attributeName] === $attributeValue);
                default: /* Do not allow setting of arbitrary attributes. */
                    /* Return false if attempt made to set a value for an unexpected attributeName. */
                    return false;
            }
        }
        /* Return false if attempt made to set value for an $attributeName not in the
           componentAttributes array. */
        return false;
    }

    /**
     * Sets the enabled componentAttribute to false.
     *
     * @return bool True if the enabled componentAttribute was set to false, false otherwise.
     */
    public function disableApp()
    {
        return $this->setComponentAttribute('enabled', false);
    }

    /**
     * Register a dependency in the componentAttribute array's dependencies array.
     *
     * @param string $dependency Name of the dependency.
     *
     * @return bool True if the dependency was registered in the componentAttribute array's
     *              dependencies array, false otherwise.
     */
    public function registerDependency(string $dependency)
    {
        return $this->setComponentAttribute('dependencies', $dependency);
    }

    /**
     * Register a theme in the componentAttribute array's themes array.
     *
     * @param string $theme Name of the theme.
     *
     * @return bool True if the theme was registered in the componentAttribute array's
     *              themes array, false otherwise.
     */
    public function registerTheme(string $theme)
    {
        return $this->setComponentAttribute('themes', $theme);
    }

    /**
     * Set a custom attribute in the componentAttribute array's customAttributes array.
     *
     * @param string $customAttributeKey A key to use for the custom attribute.
     *
     * @param $customAttributeValue mixed The value to set for the custom attribute.
     *
     * @return bool True if the custom attribute was set in the componentAttribute array's
     *              customAttributes array under the specified $customAttributeKey, false otherwise.
     */
    public function setCustomAttribute(string $customAttributeKey, $customAttributeValue)
    {
        return $this->setComponentAttribute('customAttributes', $customAttributeValue, $customAttributeKey);
    }
}