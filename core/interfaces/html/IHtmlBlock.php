<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/18/18
 * Time: 11:39 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlBlock. Defines the basic contract of an object that defines a block of html based on assigned
 * IHtml implementations.
 * @package DarlingCms\interfaces\html
 */
interface IHtmlBlock extends IHtml
{
    /**
     * Adds an IHtml implementation to the block.
     * @param IHtml $html The IHtml implementation to add to the block.
     * @return bool Returns true if the IHtml implementation was added to the block, false otherwise.
     */
    public function addHtml(IHtml $html): bool;

    /**
     * Returns an array of the IHtml implementations assigned to this html block.
     * @return array An array of the IHtml implementations assigned to this html block.
     */
    public function getHtmlArray(): array;

    /**
     * Returns the string of html constructed from the IHtml implementations assigned to this html block.
     * @return string The string of html constructed from the IHtml implementations assigned to this html block.
     */
    public function getHtml(): string;

}
