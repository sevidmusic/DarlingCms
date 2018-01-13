<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/12/17
 * Time: 9:10 PM
 */

namespace DarlingCms\classes\component\html;

/**
 * Class html. Defines an object whose responsibility is to generate a string of html based on certain parameters.
 * This class implements the \DarlingCms\abstractions\component\Acomponent abstract class. This class can be used as
 * is, or can be used as a base class for more specified html objects. This class does it's best to ensure the html
 * it generates is valid, standards compliant, html 5.
 *
 * @package DarlingCms\classes\component
 */
class html extends \DarlingCms\abstractions\component\Acomponent
{
    /**
     * @var string The content that goes between the tags, only used with oc types.
     */
    protected $content = ''; // set to empty string to ensure string type on instantiation.
    //@todo: Review oc and uc arrays to double check tags are in appropriate array and that none are missing...
    /**
     * @var string The tag type.
     */
    protected $tagType;
    /**
     * @var string The html.
     */
    protected $html = '';
    /**
     * @var array Array of valid html tag types that require an opening and closing html tag.
     */
    private $ocTagTypes = array(
        '!--',
        'a',
        'abbr',
        'address',
        'article',
        'aside',
        'audio',
        'b',
        'bdi',
        'bdo',
        'blockquote',
        'body',
        'button',
        'canvas',
        'caption',
        'cite',
        'code',
        'colgroup',
        'datalist',
        'dd',
        'del',
        'details',
        'dfn',
        'dialog',
        'div',
        'dl',
        'dt',
        'em',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'head',
        'header',
        'html',
        'i',
        'iframe',
        'ins',
        'kbd',
        'label',
        'legend',
        'li',
        'menu', /* Firefox >=8 only... */
        'menuitem', /* Firefox >= 8 only... */
        'meter',
        'nav',
        'noscript',
        'object',
        'ol',
        'optgroup',
        'option',
        'output',
        'p',
        'picture',
        'pre',
        'progress',
        'q',
        'rp',
        'rt',
        'ruby',
        's',
        'samp',
        'script',
        'section',
        'select',
        'small',
        'sup',
        'table',
        'summary',
        'tbody',
        'td',
        'textarea',
        'tfoot',
        'th',
        'thead',
        'time',
        'title',
        'tr',
        'track',
        'u',
        'ul',
        'var',
        'video',
        'p',
    );
    /**
     * @var array Array of valid html tag types that  only require an opening html tag.
     */
    private $ucTagTypes = array(
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'main',
        'map',
        'mark',
        'meta',
        'param',
        'source',
        'span',
        'strong',
        'style',
        'sub',
    );

    /**
     * html constructor.
     * @param string $tagType
     * @param string $content
     * @param array $attributes
     */
    public function __construct(string $tagType, string $content = '', array $attributes = array())
    {
        $this->setComponentAttributes($attributes);
        $this->setComponentName($tagType);
        if (in_array($tagType, $this->ocTagTypes, true) || in_array($tagType, $this->ucTagTypes, true)) {
            $this->tagType = $tagType;
            $this->content = trim($content);
            $this->generateHtml();
        }
        parent::__construct();
    }

    /**
     * Set component attributes array.
     * @param array $attributes The array to set as the component attributes.
     * @return bool True if $attributes were set, false otherwise.
     */
    public function setComponentAttributes(array $attributes)
    {
        $this->componentAttributes = $attributes;
        return isset($this->componentAttributes);
    }

    /**
     * Set the component name.
     * @param string $name
     * @return bool True if component name was set, false otherwise.
     */
    public function setComponentName(string $name)
    {
        if (isset($this->componentName) === false) {
            $this->componentName = $name;
        }
        return (isset($this->componentName) && ($this->componentName === $name));
    }

    /**
     * Generates the html.
     * @return bool True if html was generated and set and assigned to the html property, false otherwise.
     */
    public function generateHtml()
    {// @todo: You may need to also check the oc tag is in the oc tags array...?
        return (in_array($this->tagType, $this->ucTagTypes, true) ? $this->generateUcTag() : $this->generateOcTag());
    }

    /**
     * Generate html without the closing tag.
     * @return bool True if html was generated and set and assigned to the html property, false otherwise.
     */
    private function generateUcTag()
    {
        $attributes = $this->getComponentAttributes();
        switch (empty($attributes)) {
            case true:
                $this->html = str_replace(' >', '>', "<$this->tagType>");
                break;
            case false:
                $this->html = str_replace(' >', '>', "<$this->tagType " . implode(' ', $this->getComponentAttributes()) . ">");
                break;
        }
        return isset($this->html);
    }

    /**
     * Generates html with open and closing tag.
     * @return bool True if html was generated and set and assigned to the html property, false otherwise.
     */
    private function generateOcTag()
    {
        $attributes = $this->getComponentAttributes();
        switch (empty($attributes)) {
            case true:
                $this->html = str_replace(' >', '>', "<$this->tagType>$this->content</$this->tagType>");
                break;
            case false:
                $this->html = str_replace(' >', '>', "<$this->tagType " . implode(' ', $this->getComponentAttributes()) . ">$this->content</$this->tagType>");
                break;
        }
        return isset($this->html);
    }

    /**
     * Returns the html via getHtml().
     *
     * @return string The html.
     *
     */
    function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Returns the generated html.
     * @return string The generated html.
     */
    public function getHtml()
    {
        $tagId = 'NO ID';
        foreach ($this->getComponentAttributes() as $attribute) {
            if (substr($attribute, 0, 2) === 'id') {
                $tagId = $attribute;
            }
        }
        $tagId = ($tagId === 'NO ID' ? '<' . $this->tagType . '>' : $tagId);
        return PHP_EOL . '<!-- Begin ' . $tagId . ' -->' . PHP_EOL . $this->html . PHP_EOL . '<!-- End ' . $tagId . ' -->' . PHP_EOL;
    }

    /**
     * @inheritdoc
     */
    function __debugInfo()
    {
        return array(
            'tagType' => $this->tagType,
            'html' => $this->html,
            'content' => $this->content,
            'componentAttributes' => $this->getComponentAttributes(),
        );
    }
}
