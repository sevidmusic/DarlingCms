<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/18
 * Time: 11:31 PM
 */

namespace DarlingCms\classes\html;


use DarlingCms\interfaces\html\IHtml;
use DarlingCms\interfaces\html\IHtmlContainer;

/**
 * Class HtmlContainer. Defines an implementation of the IHtmlContainer interface that can be used to
 * wrap a group of IHtml implementation instances assigned to an HtmlBlock within a specified tag. This
 * class uses an injected HtmlBlock instance to govern the IHtml implementation instances, and extends
 * the HtmlTag object to create the html for the tag used to wrap the injected IHtmlBlock instance's html.
 * @package DarlingCms\classes\html
 * @see HtmlContainer::addHtml()
 * @see HtmlContainer::getAttributes()
 * @see HtmlContainer::getContent()
 * @see HtmlContainer::getHtml()
 * @see HtmlContainer::getHtmlArray()
 * @see HtmlContainer::getTagType()
 * @see HtmlBlock
 */
class HtmlContainer extends HtmlTag implements IHtmlContainer
{
    /**
     * @var HtmlBlock HtmlBlock instance that governs the IHtml implementation instances.
     */
    protected $htmlBlock;

    /**
     * HtmlContainer constructor. Injects the HtmlBlock instance used to govern the IHtml implementation instances,
     * and sets the tag type and attributes of the html tag used to wrap the injected IHtmlBlock instance's html.
     * @param HtmlBlock $htmlBlock Instance of an HtmlBlock object. Note: It is ok to pass an HtmlBlock instance
     *                             that already is assigned one or more IHtml implementation instances. To add
     *                             additional IHtml instances to the container after this class is instantiated
     *                             call the HtmlContainer class's addHtml() method.
     * @param string $tagType The tag type of the tag used to wrap the HtmlBlock's html.
     * @param array $attributes Array of attribute name/value pairs to assign to the html tag used to wrap the
     *                          HtmlBlock's html. Note: Keys will be used as attribute names, and values will be
     *                          used as attribute values, i.e., array('attributeName' => 'attributeValue'). To
     *                          assign an attribute that has no value just specify the attribute name as a value,
     *                          i.e., array('attributeName').
     */
    public function __construct(HtmlBlock $htmlBlock, string $tagType, array $attributes = array())
    {
        $this->htmlBlock = $htmlBlock;
        parent::__construct($tagType, $attributes);
    }

    /**
     * Adds an IHtml implementation instance to the container.
     * @param IHtml $html The IHtml implementation instance to add to the container.
     * @return bool Returns true if the IHtml implementation instance was added to the container, false otherwise.
     */
    public function addHtml(IHtml $html): bool
    {
        return $this->htmlBlock->addHtml($html);
    }

    /**
     * Returns an array of the IHtml implementations assigned to this html container.
     * @return array An array of the IHtml implementations assigned to this html container.
     */
    public function getHtmlArray(): array
    {
        return $this->htmlBlock->getHtmlArray();
    }

    /**
     * Returns the html string constructed from the IHtml implementation instances assigned to this container.
     * Note: This method will return a string of html constructed from the IHtml implementation instances assigned
     * to this container excluding the container's html tag, i.e., the injected HtmlBlock instance's html.
     * @return string The html string constructed from the IHtml implementations assigned to this container, i.e.,
     *                the injected HtmlBlock instance's html.
     */
    public function getContent(): string
    {
        return $this->htmlBlock->getHtml();
    }
}
