<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/18/18
 * Time: 11:45 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlContainer. Defines the basic contract of an object that defines string of html based on a group
 * of IHtml implementations that are wrapped in an html tag.
 * @package DarlingCms\interfaces\html
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
     * Returns an array of the IHtml implementations assigned to this html container.
     * @return array An array of the IHtml implementations assigned to this html container.
     */
    public function getHtmlArray(): array;

    /**
     * Returns the tag type of the html container, i.e., the tag used to wrap the IHtml implementations.
     * @return string The tag type of the html container.
     */
    public function getTagType(): string;

    /**
     * Returns an array of attribute name/value pairs assigned to the html container, i.e., the attributes
     * assigned to the tag used to wrap the IHtml implementations.
     * @return array An array of attribute name/value pairs assigned to the html container.
     */
    public function getAttributes(): array;

    /**
     * Returns the html string constructed from the IHtml implementations assigned to this container.
     * @return string The html string constructed from the IHtml implementations assigned to this container.
     */
    public function getContent(): string;

    /**
     * Returns the html string constructed from the container's tag type, attributes, and content.
     * @return string The the html string constructed from the container's tag type, attributes, and content.
     */
    public function getHtml(): string;

}
