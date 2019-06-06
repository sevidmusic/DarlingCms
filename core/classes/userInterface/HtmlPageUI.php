<?php


namespace DarlingCms\classes\userInterface;


use DarlingCms\classes\staticClasses\utility\StringUtility;
use DarlingCms\interfaces\dataStructures\IAppRegion;
use DarlingCms\interfaces\dataStructures\IHtmlPage as IHtmlPageDS;
use DarlingCms\interfaces\html\IHtmlPage;
use DarlingCms\interfaces\startup\IMultiAppStartup;
use DarlingCms\interfaces\userInterface\IUserInterface;

/**
 * Class HtmlPageUI. Defines an implementation of the IHtmlPage and IUserInterface
 * interfaces that extends the CoreHtmlUserInterface class. This implementation
 * is specifically designed to utilize an IHtmlPage implementation instance to
 * generate a user interface made up of app output organized into various
 * regions of an html page.
 * @package DarlingCms\classes\userInterface
 */
class HtmlPageUI extends CoreHtmlUserInterface implements IHtmlPage, IUserInterface
{
    /**
     * @var IMultiAppStartup Local instance of an object that implements the
     *                       IMultiAppStartup interface.
     */
    protected $appStartup;

    /**
     * @var IHtmlPageDS Local instance of an object that implements the
     *                        IHtmlPage interface.
     */
    private $htmlPage;

    /**
     * HtmlPageUI constructor. Injects the IMultiAppStartup and IHtmlPage implementation
     * instances used by this HtmlPageUI instance.
     * @param IMultiAppStartup $appStartup The IMultiAppStartup implementation instance
     *                                     responsible for starting up the apps that
     *                                     are assigned to this html page.
     * @param IHtmlPageDS $htmlPage The IHtmlPage implementation instance that represents
     *                              the html page to generate a user interface for.
     */
    public function __construct(IMultiAppStartup $appStartup, IHtmlPageDS $htmlPage)
    {
        parent::__construct($appStartup);
        $this->htmlPage = $htmlPage;
        $this->title = $htmlPage->getName();
    }

    /**
     * Get the html page's body.
     *
     * @return string The html page's body.
     */
    public function getBody(): string
    {
        $body = '';
        /**
         * @var IAppRegion $appRegion
         */
        foreach ($this->htmlPage->getAppRegions() as $appRegion) {
            $body .= $this->getRegionOutput($appRegion);
        }
        return '<body>' . $body . '</body>';
    }

    /**
     * Get the app output of a specified region.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance whose app out
     *                              should be returned.
     *
     * @return string The specified app region's app output.
     */
    private function getRegionOutput(IAppRegion $appRegion): string
    {
        $regionOutput = '';
        foreach ($appRegion->getAppNames() as $appName) {
            $regionOutput .= $this->appStartup->getSpecifiedAppOutput($appName);
        }
        if ($appRegion->getType() === $appRegion::CONTAINED) {
            return "<div class=\"{$this->formatRegionName($appRegion)}\">{$regionOutput}</div>";
        }
        return $regionOutput;
    }

    /**
     * Filters whitespace and non-alphanumeric characters from the specified app
     * region's name.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance whose
     * name is to be filtered.
     *
     * @return string The specified app region's filtered name.
     */
    private function formatRegionName(IAppRegion $appRegion): string
    {
        return str_replace(' ', '', StringUtility::filterAlphaNumeric($appRegion->getName()));
    }
}
