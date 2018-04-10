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
     * @var int Used to set position of html objects that should be incorporated into the opening html.
     */
    const OPENING = 1;

    /**
     * @var int Used to set position of html objects that should be incorporated into the html in between
     *          the opening html and the closing html.
     */
    const MIDDLE = 2;

    /**
     * @var int Used to set position of html objects that should be incorporated into the closing html.
     */
    const CLOSING = 4;

    /**
     * @var array Array of html attributes to assign to this user interface's html container.
     */
    private $userInterfaceAttributes = array();

    /**
     * @var array Array of html objects that will be appended to the beginning of the user interface's htmlContainer.
     */
    private $openingHtml = array();

    /**
     * @var array Array of html objects that will be added to the user interface's htmlContainer between
     *            the html objects assigned to the $openingHtml and $closingHtml property arrays.
     */
    private $html = array();

    /**
     * @var array Array of html objects that will be appended to the end of the user interface's htmlContainer.
     */
    private $closingHtml = array();

    /**
     * @var string The type of html tag to assign to this user interface's htmlContainer.
     */
    public $containerType = 'div';

    /**
     * userInterface constructor. Sets the user interface's html container's id attribute. Assigns each
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
     * Note: If this method is called before the addOpeningHtml(), addClosingHtml(), addPageContent(),
     * addContent(), or addHtml() methods, any html objects added by those methods will not be included
     * in the user interface's htmlContainer. i.e., the add*() methods must be called before getUserInterface()
     * to have their additions included in the user interface.
     * @param html ...$html The html objects to append the user interface's htmlContainer.
     * Note: Any html objects passed directly to getUserInterface() will be incorporated in between the
     * html objects that were added via the addOpeningHtml() and addClosingHtml() methods respectively.
     * i.e., Objects passed directly to getUserInterface() will assume the MIDDLE position.
     * Note: Any objects passed directly to getUserInterface() will be incorporated after html objects
     * that were added via the addPageContent(), addContent(), or addHtml() method and were assigned to
     * the MIDDLE position.
     * @return htmlContainer Returns the htmlContainer instance responsible for this user interface's html.
     */
    public function getUserInterface(html ...$html): htmlContainer
    {
        if (empty(array_merge($this->openingHtml, $this->html, $this->closingHtml)) === false) {
            /* Instantiate an htmlContainer for this user interface. */
            $userInterface = new htmlContainer($this->containerType, $this->userInterfaceAttributes);
            /* Append any html objects assigned to the $openingHtml property's array. */
            if (!empty($this->openingHtml)) {
                foreach ($this->openingHtml as $openingHtml) {
                    $userInterface->appendHtml($openingHtml);
                }
            }
            /* Append any html objects assigned to the $html properties array. */
            foreach ($this->html as $htmls) {
                $userInterface->appendHtml($htmls);
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
        return new htmlContainer('!--', [$this->userInterfaceAttributes[0] . ' Placeholder']);
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
     * Add html content to specified pages.
     * @param array $pages Array of page names to add the content to.
     * @param html $content The html object instance responsible for the content's html.
     * @param int $position (optional) If set, these flags are used to determine the position(s)
     *                                 the html content will be assigned to.
     *                                 Note: Multiple position flags can be used to assign the html
     *                                 content to multiple positions.
     *                                 -- Options --
     *                                 OPENING: If set, html content will be assigned to the opening html.
     *                                 MIDDLE: If set, html content will be assigned to the the middle html,
     *                                         i.e., between the OPENING and CLOSING html.
     *                                 CLOSING: If set, html content will be assigned to the closing html.
     * @return bool True if content was added, false otherwise. Note: This method will return false
     *              if the current page is not one of the pages specified by the $pages parameter.
     */
    public function addPageContent(array $pages, html $content, $position = 2): bool
    {
        if (in_array(filter_input(INPUT_GET, 'page'), $pages, true)) {

            return $this->addContent($content, $position);
        }
        return false;
    }


    /**
     * Add html content.
     * @param html $content The html content to add.
     * @param int $position (optional) If set, these flags are used to determine the position(s)
     *                                 the html content will be assigned to.
     *                                 Note: Multiple position flags can be used to assign the html
     *                                 content to multiple positions.
     *                                 -- Options --
     *                                 OPENING: If set, html content will be assigned to the opening html.
     *                                 MIDDLE: If set, html content will be assigned to the the middle html,
     *                                         i.e., between the OPENING and CLOSING html.
     *                                 CLOSING: If set, html content will be assigned to the closing html.
     * @return bool True if the content was added, false otherwise.
     */
    public function addContent(html $content, $position = 2): bool
    {
        $status = array();
        if ($position & self::OPENING) {
            array_push($status, $this->addOpeningHtml($content));
        }
        if ($position & self::MIDDLE) {
            array_push($status, $this->addHtml($content));
        }
        if ($position & self::CLOSING) {
            array_push($status, $this->addClosingHtml($content));
        }
        return in_array(false, $status, true);
    }

    /**
     * Add html objects to the user interface's html container.
     * Note: Html objects added with this method will be appended to the user interface's htmlContainer
     * between the html objects assigned to the $openingHtml and $closingHtml property arrays, i.e.,
     * the MIDDLE position.
     * Note: This method must be called before the getUserInterface() method or it will have no effect.
     * @param html $html The html object to added to the user interface's html container.
     * @return bool True if html objects were added, false otherwise.
     */
    public function addHtml(html $html)
    {
        $initialCount = count($this->html);
        array_push($this->html, $html);
        return count($this->html) > $initialCount;
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
