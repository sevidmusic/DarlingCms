<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/18/18
 * Time: 11:26 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtml. Defines the basic contract of an object that defines a string of html.
 * @package DarlingCms\interfaces\html
 */
interface IHtml
{
    /**
     * Returns the string of html.
     * @return string The string of html.
     */
    public function getHtml(): string;
}
