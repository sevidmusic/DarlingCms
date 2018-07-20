<?php
/**
 * Created by Sevi Donnelly Foreman
 * Date: 5/18/18
 * Time: 11:45 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlContainer. Defines the basic contract of an object that constructs a string of html based on
 * a group of IHtml implementations that are wrapped in an html tag.
 * @package DarlingCms\interfaces\html
 * @see IHtmlContainer::addHtml()
 * @see IHtmlContainer::getHtmlArray()
 * @see IHtmlContainer::getTagType()
 * @see IHtmlContainer::getAttributes()
 * @see IHtmlContainer::getContent()
 * @see IHtmlContainer::getHtml()
 */
interface IHtmlContainer extends IHtmlTag, IHtmlBlock
{
    /**
     * Adds an IHtml implementation to the container.
     * @param IHtml $html The IHtml implementation to add to the container.
     * @return bool Returns true if the IHtml implementation was added to the container, false otherwise.
     */
    public function addHtml(IHtml $html): bool;

    /**
     * Returns an array of the IHtml implementations assigned to this html container, or an
     * empty array if there aren't any IHtml implementations assigned to this html container.
     * @return array An array of the IHtml implementations assigned to this html container, or
     *               an empty array if there aren't any IHtml implementations assigned to this
     *               html container.
     */
    public function getHtmlArray(): array;

    /**
     * Returns the tag type of the html container, i.e., the type of tag used to wrap the IHtml implementations,
     * i.e., the type of the outermost tag of the html block.
     * @return string The tag type of the html container.
     */
    public function getTagType(): string;

    /**
     * Returns an array of attribute name/value pairs assigned to the html container's tag, i.e., the attributes
     * assigned to the tag used to wrap the IHtml implementations. This method will return an empty array
     * if the html container is not assigned any attributes.
     * @return array An array of attribute name/value pairs assigned to the html container, or an empty
     *               array if the html container is not assigned any attributes.
     */
    public function getAttributes(): array;

    /**
     * Returns the html string constructed from the IHtml implementations assigned to this container, excluding
     * the html container's opening and closing html tags.
     * @return string The html string constructed from the IHtml implementations assigned to this container,
     *                excluding the html container's opening and closing html tags.
     */
    public function getContent(): string;

    /**
     * Returns the html string constructed from the html container's tag type, attributes, and the collective html
     * of the assigned IHtml implementations.
     * @return string The the html string constructed from the container's tag type, attributes, and the
     *                collective html of the assigned IHtml implementations.
     */
    public function getHtml(): string;

}
