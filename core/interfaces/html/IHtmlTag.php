<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/18/18
 * Time: 11:29 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlTag. Defines the basic contract of an object that constructs a string of html based on tag type,
 * attributes, and content.
 * @package DarlingCms\interfaces\html
 */
interface IHtmlTag extends IHtml
{
    /**
     * Returns the tag type.
     * @return string The tag type.
     */
    public function getTagType(): string;

    /**
     * Returns an array of attribute name/value pairs assigned to the html tag where names are keys and
     * values are values, or an empty array if the html tag is not assigned any attributes.
     * @return array An array of attributes assigned to the html tag, or an empty array if the html tag is not
     *               assigned any attributes.
     */
    public function getAttributes(): array;

    /**
     * Returns the html tag's content, or an empty string if the tag does not have any content.
     * @return string The html tag's content, or an empty string if the tag does not have any content.
     */
    public function getContent(): string;

    /**
     * Returns the string of html constructed from the tag type, attributes, and content.
     * @return string Returns the string of html constructed from the tag type, attributes, and content.
     */
    public function getHtml(): string;
}
