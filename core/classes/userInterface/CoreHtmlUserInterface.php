<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/28/18
 * Time: 10:08 AM
 */

namespace DarlingCms\classes\userInterface;

use DarlingCms\interfaces\html\IHtmlPage;
use DarlingCms\interfaces\startup\IAppStartup;
use DarlingCms\interfaces\userInterface\IUserInterface;

/**
 * Class CoreHtmlUserInterface. Defines an implementation of the IUserInterface and IHtmlPage interfaces that extends
 * the DOMDocument class. This class is responsible for generating the html user interface for the Darling Cms.
 * @package DarlingCms\classes\userInterface
 * @see CoreHtmlUserInterface::getDoctype()
 * @see CoreHtmlUserInterface::getHead()
 * @see CoreHtmlUserInterface::getBody()
 * @see CoreHtmlUserInterface::getUserInterface()
 * @see CoreHtmlUserInterface::setCssLinkTags()
 * @see CoreHtmlUserInterface::setJsLinkTags()
 * @see CoreHtmlUserInterface::formatHtml()
 */
class CoreHtmlUserInterface extends \DOMDocument implements IHtmlPage, IUserInterface
{
    /**
     * @var IAppStartup $appStartup Local instance of an object that implements the IAppStartup interface.
     */
    private $appStartup;

    /**
     * @var array Array of css link tags to assign to the head.
     */
    private $headCssLinksTags = array();

    /**
     * @var array Array of script tags to assign to the head.
     */
    private $headScriptsTags = array();

    /**
     * CoreHtmlUserInterface constructor. Injects the IAppStartup instance used by this object, and calls the
     * IAppStartup implementation's startup() method. Additionally, calls the setCssLinkTags() method, and the
     * setJsScriptTags() method.
     * @param IAppStartup $appStartup Instance of an object that implements the IAppStartup interface.
     * @see IAppStartup::startup()
     * @see CoreHtmlUserInterface::setCssLinkTags()
     * @see CoreHtmlUserInterface::setJsLinkTags()
     */
    public function __construct(IAppStartup $appStartup)
    {
        $this->appStartup = $appStartup;
        $this->appStartup->startup();
        $this->setCssLinkTags();
        $this->setJsLinkTags();
    }

    /**
     * Returns the Doctype.
     * @return string The Doctype.
     */
    public function getDoctype(): string
    {
        return '<!DOCTYPE html>';
    }

    /**
     * Returns the head.
     * @return string The head.
     */
    public function getHead(): string
    {
        return '<head><title>Darling Cms</title>' . implode('', $this->headCssLinksTags) . implode('', $this->headScriptsTags) . '</head>';
    }

    /**
     * Returns the body. The body's content is determined by the string returned by the IAppStartup instance's
     * getAppOutput() method.
     * @return string The body.
     * @see IAppStartup::getAppOutput()
     */
    public function getBody(): string
    {
        return '<body>' . $this->appStartup->getAppOutput() . '</body>';
    }

    /**
     * Gets the user interface.
     * @return string The user interface.
     * @see CoreHtmlUserInterface::formatHtml()
     * @see CoreHtmlUserInterface::getDoctype()
     * @see CoreHtmlUserInterface::getHead()
     * @see CoreHtmlUserInterface::getBody()
     */
    public function getUserInterface(): string
    {
        return $this->formatHtml($this->getDoctype() . '<html lang="en">' . $this->getHead() . $this->getBody() . '</html>');
    }

    /**
     * Set the link tags for any css file paths returned by the IAppStartup implementation's getCssPaths() method.
     * @see IAppStartup::getCssPaths()
     */
    private function setCssLinkTags(): void
    {
        foreach ($this->appStartup->getCssPaths() as $cssPath) {
            array_push($this->headCssLinksTags, "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssPath}\">");
        }
    }

    /**
     * Set the script tags for any javascript file paths returned by the IAppStartup implementation's getJsPaths()
     * method.
     * @see IAppStartup::getJsPaths()
     */
    private function setJsLinkTags(): void
    {
        foreach ($this->appStartup->getJsPaths() as $jsPath) {
            /* Html comment added between script tags to prevent formatting from replacing closing script tag with />,
             * this is a hack till a workaround is found. */
            array_push($this->headCssLinksTags, '<script src="' . $jsPath . '"><!-- --></script>');
        }
    }

    /**
     * Prep html for formatting. This method transforms a string of html that spans multiple lines into
     * a single line of html, removing excessive or unnecessary whitespace, tabs, and new lines.
     * @param string $html The html to prep.
     * @return string The prepped html.
     */
    private function prepHtml(string $html): string
    {
        return str_replace(array('> '), '>', preg_replace('/\s+/', ' ', $html));
    }

    /**
     * Formats html.
     * @param string $html The html to be formatted.
     * @return string The formatted html.
     * @see \DOMDocument::loadHTML()
     * @see \DOMDocument::saveXML()
     * @credit Html formatting solution using DOMDocument::saveXml() instead of DOMDocument::saveHtml() was found
     *         on Stack Overflow:
     * @see https://stackoverflow.com/questions/768215/php-pretty-print-html-not-tidy
     */
    private function formatHtml(string $html)
    {
        /* Make sure whitespace is ignored. */
        $this->preserveWhiteSpace = false;
        /* Load $html into the $dom. */
        $this->loadHTML($this->prepHtml($html));
        /* Make sure html will be formatted. */
        $this->formatOutput = true;
        /*
         * Return the formatted html by calling the DOMDocument::saveXml() method to insure elements are formatted with
         * tabs, not just new lines.
         * @see https://stackoverflow.com/questions/768215/php-pretty-print-html-not-tidy for further explanation of
         * why the DOMDocument::saveXml() method is used instead of the DOMDocument::saveHtml() method.
         */
        return $this->cleanHtml($this->saveXML());
    }

    /**
     * This method removes the unnecessary strings added by saveXML().
     * @param string $html The string returned by saveXml().
     * @return string The cleansed string.
     */
    private function cleanHtml(string $html): string
    {
        return str_replace(array('<![CDATA[ ]]>', '<![CDATA[<!-- -->]]>', '<?xml version="1.0" standalone="yes"?>' . PHP_EOL, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PHP_EOL), '', $html);
    }
}
