<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/19/18
 * Time: 10:40 AM
 */

namespace DarlingCms\classes\html;


use DarlingCms\interfaces\html\IHtmlTag;

/**
 * Class HtmlTag. Defines an implementation of the IHtmlTag interface that can be used to generate
 * an html string based on a specified tag type, attributes, and content.
 * @package DarlingCms\classes\html
 */
class HtmlTag implements IHtmlTag
{
    /**
     * @var string The tag type.
     */
    protected $tagType;

    /**
     * @var array Array of attributes to assign to the html tag, or an empty array if are no assigned attributes.
     */
    protected $attributes = array();

    /**
     * @var string The html tags content, or an empty string if the html tag has no content.
     */
    protected $content = '';

    /**
     * @var bool True if closing tag is set to be excluded, false otherwise.
     */
    protected $excludeClosingTag = false;

    public function __construct(string $tagType, array $attributes = array(), string $content = '', bool $excludeClosingTag = false)
    {
        $this->tagType = $tagType;
        $this->attributes = $attributes;
        $this->content = $content;
        $this->excludeClosingTag = $excludeClosingTag;
    }

    /**
     * Returns the tag type.
     * @return string The tag type.
     */
    public function getTagType(): string
    {
        return $this->tagType;
    }

    /**
     * Returns an array of attribute name/value pairs assigned to the html tag where names are keys and values
     * are values.
     * Note: If the tag is not assigned any attributes, this method will return an empty array.
     * @return array An array of attributes assigned to the html tag, or an empty array if the tag is not
     *               assigned any attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns the html tag's content, or an empty string if the tag does not have any content.
     * @return string The html tag's content, or an empty string if the tag does not have any content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Returns the string of html constructed from the tag type, attributes, and content.
     * @return string Returns the string of html constructed from the tag type, attributes, and content.
     */
    public function getHtml(): string
    {
        if ($this->excludeClosingTag() === true) {
            return '<' . $this->getTagType() . $this->getAttributeString() . '>' . $this->getContent();
        }
        return '<' . $this->getTagType() . $this->getAttributeString() . '>' . $this->getContent() . '</' . $this->getTagType() . '>';
    }

    /**
     * Constructs an attribute string based on the attributes assigned to the tag.
     * @return string The attribute string.
     */
    protected function getAttributeString(): string
    {
        $attributes = '';
        foreach ($this->getAttributes() as $name => $value) {
            if (is_array($value) === true) {
                $value = implode(' ', $value);
            }
            switch ($name === $value || is_int($name) === true) {
                case true:
                    $attributes .= ' ' . strval($value);
                    break;
                default:
                    $attributes .= ' ' . strval($name) . '="' . strval($value) . '"';
                    break;
            }
        }
        return $attributes;
    }

    /**
     * Returns true if the closing tag is set to be excluded, false otherwise.
     * @return bool True if the closing tag is set to be excluded, false otherwise.
     */
    protected function excludeClosingTag(): bool
    {
        return $this->excludeClosingTag;
    }
}
