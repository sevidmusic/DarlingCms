<?php

namespace DarlingCms\classes\userInterface;

use DarlingCms\classes\component\html\html;
use DarlingCms\classes\component\html\htmlContainer;
use DarlingCms\interfaces\userInterface\IuserInterface;

/**
 * Class userInterface. Defines a base implementation of the IuserInterface interface. This
 * class can be extended to define more niche user interface objects, or used as is to create
 * simple user interfaces.
 * @package DarlingCms\abstractions\userInterface
 */
class userInterface implements IuserInterface
{
    /**
     * @var array Array of html attributes to assign to this user interface's html container.
     */
    private $userInterfaceAttributes = array();

    /**
     * @var array Array of html objects that will be appended to the beginning of the user interface's htmlContainer.
     */
    private $openingHtml = array();

    /**
     * @var array Array of html objects that will be appended to the end of the user interface's htmlContainer.
     */
    private $closingHtml = array();

    /**
     * @var string The type of html tag to assign to this user interface's htmlContainer.
     */
    public $containerType = 'div';

    /**
     * userInterface constructor. Set the user interface's html container's id attribute. Assigns each
     * of the specified css class names to the user interface's html container's class attribute.
     * @param string $userInterfaceHtmlId (optional) The html id to assign to the user interface's html container.
     * @param array $userInterfaceCssClasses (optional) Array of css class names to assign to the user interface's
     *                                                  html container.
     * @see userInterface::setUserInterfaceAttribute()
     */
    public function __construct(string $userInterfaceHtmlId = '', array $userInterfaceCssClasses = array())
    {
        if ($userInterfaceHtmlId !== '') {
            /* Set the user interfaces html container's id attribute. */
            $this->setUserInterfaceAttribute('id', $userInterfaceHtmlId);
            if (!empty($userInterfaceCssClasses)) {
                /* Assign each of the specified css class names to the user interface's html container's class attribute. */
                $this->setUserInterfaceAttribute('class', implode(' ', $userInterfaceCssClasses));
            }
        }
    }

    /**
     * Returns the html container for this user interface.
     * Note: If this method is called before the addOpeningHtml() or addClosingHtml() methods
     * any html objects added by those methods will not be included in the user interface's
     * htmlContainer.
     * @param html ...$html The html objects to append the user interface's htmlContainer.
     * Note: Any html objects passed to getUserInterface() will be added in between the
     * html objects that were added via the addOpeningHtml() and addClosingHtml()
     * methods respectively.
     * @return htmlContainer Returns the htmlContainer instance responsible for this user interface's html.
     */
    public function getUserInterface(html ...$html): htmlContainer
    {
        /* Instantiate an htmlContainer for this user interface. */
        $userInterface = new htmlContainer($this->containerType, $this->userInterfaceAttributes);
        /* Append any html objects assigned to the $openingHtml property's array. */
        if (!empty($this->openingHtml)) {
            foreach ($this->openingHtml as $openingHtml) {
                $userInterface->appendHtml($openingHtml);
            }
        }
        /* Append any html objects passed directly to this method. */
        foreach ($html as $content) {
            $userInterface->appendHtml($content);
        }
        /* Append any html objects assigned to the $closingHtml property's array. */
        if (!empty($this->closingHtml)) {
            foreach ($this->closingHtml as $closingHtml) {
                $userInterface->appendHtml($closingHtml);
            }
        }
        /* Return the user interface's htmlContainer. */
        return $userInterface;
    }

    /**
     * Assign an html attribute to the user interface's html container.
     * Note: To set an attribute that has no value, don't specify an $attributeValue.
     * @param string $attributeName The name of the attribute to assign.
     * @param string $attributeValue (optional) The attribute's value.
     * @return bool True if the attribute was added, false otherwise.
     */
    private function setUserInterfaceAttribute(string $attributeName, string $attributeValue = ''): bool
    {
        /* Count initial number of attributes assigned to the $userInterfaceAttributes property's array. */
        $initialCount = count($this->userInterfaceAttributes);
        /* Determine how to the attribute should be constructed and assigned to the $userInterfaceAttributes
           property's array. */
        switch ($attributeValue) {
            /* If no value was passed to the $attributeValue parameter, just add the specified $attributeName
               to the $userInterfaceAttributes property's array. */
            case '':
                array_push($this->userInterfaceAttributes, $attributeName);
                break;
            /* Generate an attribute name value string and add it to the $userInterfaceAttributes property's array. */
            default:
                array_push($this->userInterfaceAttributes, $attributeName . '="' . $attributeValue . '"');
        }
        /* Return true if the attribute was added to the $userInterfaceAttributes property's array, false otherwise.*/
        return count($this->userInterfaceAttributes) > $initialCount;
    }

    /**
     * Add html objects that should be appended to the beginning of the user interface's html container.
     * Note: This method must be called before the getUserInterface() method or it will have no effect.
     * @param html ...$html The html objects that should be added to the beginning of the user
     *                      interface's html container.
     * @return bool True if html objects were added, false otherwise.
     */
    public function addOpeningHtml(html ...$html): bool
    {
        /* Count initial number of html objects in the $openingHtml property's array. */
        $initialCount = count($this->openingHtml);
        /* Add each html object to the $openingHtml property's array. */
        foreach ($html as $openingHtml) {
            array_push($this->openingHtml, $openingHtml);
        }
        /* Return true if the html objects were added, false otherwise. */
        return count($this->openingHtml) > $initialCount;
    }

    /**
     * Add html objects that should be appended to the end of the user interface's html container.
     * Note: This method must be called before the getUserInterface() method or it will have no effect.
     * @param html ...$html The html objects that should be added to the end of the user interface's
     *                      html container.
     * @return bool True if html objects were added, false otherwise.
     */
    public function addClosingHtml(html ...$html): bool
    {
        /* Count initial number of html objects in the $closingHtml property's array. */
        $initialCount = count($this->closingHtml);
        /* Add each html object to the $closingHtml property's array. */
        foreach ($html as $closingHtml) {
            array_push($this->closingHtml, $closingHtml);
        }
        /* Return true if the html objects were added, false otherwise. */
        return count($this->closingHtml) > $initialCount;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     * @see userInterface::getUserInterface()
     * @see htmlContainer::getHtml()
     */
    public function __toString()
    {
        return $this->getUserInterface()->getHtml();
    }
}
