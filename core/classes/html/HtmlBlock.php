<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/21/18
 * Time: 10:14 PM
 */

namespace DarlingCms\classes\html;


use DarlingCms\interfaces\html\IHtml;
use DarlingCms\interfaces\html\IHtmlBlock;

/**
 * Class HtmlBlock. Defines an implementation of the IHtmlBlock interface that can be used to generate an
 * html string from the assigned IHtml implementation instances.
 * @package DarlingCms\classes\html
 * @see HtmlBlock::addHtml()
 * @see HtmlBlock::getHtmlArray()
 * @see HtmlBlock::getHtml()
 */
class HtmlBlock implements IHtmlBlock
{
    /**
     * @var array Array of IHtml implementation instances assigned to this html block.
     */
    protected $htmlArray = array();

    public function __construct(IHtml ...$html)
    {
        foreach ($html as $instance) {
            $this->addHtml($instance);
        }
    }


    /**
     * Adds an IHtml implementation to the block.
     * WARNING: The method will return false if an attempt is made to add an HtmlBlock instance to itself.
     * @param IHtml $html The IHtml implementation to add to the block.
     * @return bool Returns true if the IHtml implementation was added to the block, false otherwise.
     */
    public function addHtml(IHtml $html): bool
    {
        if ($html === $this) {
            return false;
        }
        $initialCount = count($this->htmlArray);
        return array_push($this->htmlArray, $html) > $initialCount;
    }

    /**
     * Returns an array of the IHtml implementation instances assigned to this html block.
     * Note: This method will return an empty array if there are not any IHtml implementation instances
     * assigned to this html block.
     * @return array An array of the IHtml implementation instances assigned to this html block, or an empty array
     *               if there are not any IHtml implementation instances assigned to this html block.
     */
    public function getHtmlArray(): array
    {
        return $this->htmlArray;
    }

    /**
     * Returns the string of html constructed from the IHtml implementation instances assigned to this html block.
     * Note: This method will return an empty string if there are not any assigned IHtml implementation instances.
     * @return string The string of html constructed from the IHtml implementation instances assigned to this
     *                html block, or an empty string if there are not any assigned IHtml implementation instances.
     */
    public function getHtml(): string
    {
        $html = '';
        foreach ($this->getHtmlArray() as $htmlInstance) {
            $html .= $htmlInstance->getHtml();
        }
        return $html;
    }

}
