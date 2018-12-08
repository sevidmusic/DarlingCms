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
 * Class CoreHtmlUserInterface. Defines an implementation of the IUserInterface and IHtmlPage interfaces
 * that extends the DOMDocument class. This implementation is responsible for generating the html for the
 * Darling Cms user interface.
 * @package DarlingCms\classes\userInterface
 * @see CoreHtmlUserInterface::PRESERVE_EMPTY_ELEMENTS
 * @see CoreHtmlUserInterface::getDoctype()
 * @see CoreHtmlUserInterface::getHead()
 * @see CoreHtmlUserInterface::getBody()
 * @see CoreHtmlUserInterface::getUserInterface()
 * @see CoreHtmlUserInterface::setCssLinkTags()
 * @see CoreHtmlUserInterface::setJsLinkTags()
 * @see CoreHtmlUserInterface::prepHtml()
 * @see CoreHtmlUserInterface::formatHtml()
 * @see CoreHtmlUserInterface::cleanHtml()
 */
class CoreHtmlUserInterface extends \DOMDocument implements IHtmlPage, IUserInterface
{
    /**
     * @var string String used to preserve empty elements during formatting.
     */
    const PRESERVE_EMPTY_ELEMENTS = '<!-- CORE_UI_PRESERVE_EMPTY_ELEMENT -->';

    /**
     * @var IAppStartup $appStartup Local instance of an object that implements the IAppStartup interface.
     */
    private $appStartup;

    /**
     * @var array Array of css link tags to assign to the head.
     */
    private $headCssLinksTags = array();

    /**
     * @var array Array of script tags to assign to the end of the body.
     */
    private $headScriptsTags = array();

    /**
     * CoreHtmlUserInterface constructor. Injects the IAppStartup instance used by this object, and calls the
     * IAppStartup implementation's startup() method. Additionally, calls the setCssLinkTags() method, and the
     * setJsScriptTags() method in order to populate the $headCssLinksTags property's array and the $headScriptsTags
     * property's array, respectively.
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
     * Returns the Doctype string.
     * @return string The Doctype.
     */
    public function getDoctype(): string
    {
        return '<!DOCTYPE html>';
    }

    /**
     * Returns the string of html for the html head.
     * @return string The head's html.
     */
    public function getHead(): string
    {
        $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        return '<head><title>Darling Cms</title>' . $viewport . implode('', $this->headCssLinksTags) . implode('', $this->headScriptsTags) . '</head>';
    }

    /**
     * Returns the string of html for the html body.
     *
     * Note: The body's content is determined by the string returned by the IAppStartup
     * instance's getAppOutput() method.
     *
     * Note: The $headScriptsTags property's array is imploded and appended to the end of the body's content.
     *
     * @return string The body's html.
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
        return trim($this->formatHtml($this->getDoctype() . '<html lang="en">' . $this->getHead() . $this->getBody() . '</html>'));
    }

    /**
     * Set the link tags for any css file paths returned by the IAppStartup implementation's getCssPaths() method
     * in the $headCssLinks property's array.
     * @see IAppStartup::getCssPaths()
     */
    private function setCssLinkTags(): void
    {
        foreach ($this->appStartup->getCssPaths() as $cssPath) {
            $linkTag = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssPath}\">";
            if (in_array($linkTag, $this->headCssLinksTags) === false) {
                array_push($this->headCssLinksTags, $linkTag);
            }
        }
    }

    /**
     * Set the script tags for any javascript file paths returned by the IAppStartup implementation's getJsPaths()
     * method in the $headScriptsTags property's array.
     * @see IAppStartup::getJsPaths()
     */
    private function setJsLinkTags(): void
    {
        foreach ($this->appStartup->getJsPaths() as $jsPath) {
            /* Html comment added between script tags to prevent formatting from replacing closing script tag with />,
             * this is a hack till a workaround is found. */
            $scriptTag = '<script src="' . $jsPath . '" defer><!-- --></script>';
            if (in_array($scriptTag, $this->headScriptsTags) === false) {
                array_push($this->headScriptsTags, $scriptTag);
            }
        }
    }

    /**
     * Prep html for formatting. This method transforms a string of html that spans multiple lines into
     * a single line of html, removing excessive or unnecessary whitespace, tabs, and new lines. It also
     * inserts the PRESERVE_EMPTY_ELEMENTS string between occurrences of ></ in order
     * to insure empty elements are preserved when html is processed by the formatHtml() method.
     * @param string $html The html to prep.
     * @return string The prepped html.
     * @see CoreHtmlUserInterface::PRESERVE_EMPTY_ELEMENTS
     */
    private function prepHtml(string $html): string
    {
        $html = str_replace(array('> '), '>', preg_replace('/\s+/', ' ', $html));
        $html = str_replace('></', '>' . $this::PRESERVE_EMPTY_ELEMENTS . '</', $html);
        return $html;
    }

    /**
     * Formats html.
     * @param string $html The html to be formatted.
     * @return string The formatted html.
     * @see \DOMDocument::loadHTML()
     * @see CoreHtmlUserInterface::prepHtml()
     * @see CoreHtmlUserInterface::cleanHtml()
     * @see \DOMDocument::saveXML()
     * @credit Html formatting solution using DOMDocument::saveXml() instead of DOMDocument::saveHtml() was adopted
     *         from an answer found on Stack Overflow:
     * @see https://stackoverflow.com/questions/768215/php-pretty-print-html-not-tidy
     */
    private function formatHtml(string $html)
    {
        /* Make sure whitespace is ignored. */
        $this->preserveWhiteSpace = false;
        /* Load $html into the $dom. */
        @$this->loadHTML($this->prepHtml($html)); // @todo: Figure out better way to handle syntax errors like "PHP Warning: DOMDocument::loadHTML(): Tag a-scene invalid in Entity..."
        /* Make sure html will be formatted. */
        $this->formatOutput = true;
        /*
         * Return the formatted html by passing the value returned by saveXML() to the cleanHtml() method, and
         * returning the value returned by cleanHtml().
         * Note: The DOMDocument::saveXml() method is used to insure elements are formatted with
         * tabs, not just new lines.
         * @see https://stackoverflow.com/questions/768215/php-pretty-print-html-not-tidy for further explanation of
         * why the DOMDocument::saveXml() method is used instead of the DOMDocument::saveHtml() method.
         */
        return $this->cleanHtml($this->saveXML());
    }

    /**
     * This method removes the unnecessary strings added by the DOMDocument::saveXML() method and the
     * CoreHtmlUserInterface::prepHtml() method. This method will also remove empty lines.
     * @param string $html The string returned by saveXml().
     * @return string The cleansed string.
     * @see https://stackoverflow.com/questions/709669/how-do-i-remove-blank-lines-from-text-in-php?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
     * @see CoreHtmlUserInterface::PRESERVE_EMPTY_ELEMENTS
     */
    private function cleanHtml(string $html): string
    {
        $html = str_replace(array($this::PRESERVE_EMPTY_ELEMENTS, '<![CDATA[ ]]>', '<![CDATA[<!-- -->]]>', '<?xml version="1.0" standalone="yes"?>' . PHP_EOL, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PHP_EOL), '', $html);
        /**
         * Solution for removing empty lines via preg_replace() found on stack overflow at the following url:
         * @see https://stackoverflow.com/questions/709669/how-do-i-remove-blank-lines-from-text-in-php?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
         */
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $html);
    }
}
