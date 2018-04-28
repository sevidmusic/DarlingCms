<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 11:55 PM
 */

namespace DarlingCms\interfaces\html;

/**
 * Interface IHtmlPage. Defines the basic contract of an object the defines an html page.
 * @package DarlingCms\interfaces\html
 * @see IHtml::getDoctype()
 * @see IHtml::getHead()
 * @see IHtml::getBody()
 */
interface IHtmlPage
{
    /**
     * Returns the Doctype.
     * @return string The Doctype.
     */
    public function getDoctype(): string;

    /**
     * Returns the head.
     * @return string The head.
     */
    public function getHead(): string;

    /**
     * Returns the body.
     * @return string The body.
     */
    public function getBody(): string;
}
