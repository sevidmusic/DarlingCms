<?php


namespace DarlingCms\abstractions\dataStructures;


use DarlingCms\classes\dataStructures\AppRegion;
use DarlingCms\classes\staticClasses\utility\ArrayUtility;
use DarlingCms\interfaces\dataStructures\IAppRegion;
use DarlingCms\interfaces\dataStructures\IHtmlPage;

/**
 * Class AHtmlPage. Defines an abstract implementation of the IHtmlPage interface
 * which extends the AClassifiable abstract class that can be used as a base for
 * classes that implement the IHtmlPage interface.
 *
 * @package DarlingCms\abstractions\dataStructures
 */
abstract class AHtmlPage extends AClassifiable implements IHtmlPage
{
    /**
     * @var array|IAppRegion Array of IAppRegion implementation instances that
     *                       belong to this html page.
     */
    protected $appRegions = array();

    /**
     * AHtmlPage constructor. Sets the html page's name, assigns the specified
     * $appRegions to the html page, and, if specified, sets the html page's
     * type and description.
     *
     * @param string $name The name to assign to the html page.
     *
     * @param array|IAppRegion $appRegions An array of the IAppRegion implementation
     *                                     instances that represent the app regions
     *                                     to assign to the html page.
     *
     * @param string $type The type to assign to the html page.
     *
     * @param string $description The description to assign to the html page.
     *
     */
    public function __construct(string $name, array $appRegions = array(), string $type = '', string $description = '')
    {
        $this->appRegions = $appRegions;
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }


    /**
     * Adds an app region to the html page.
     *
     * Note: This method will add the app region to the end of the html page,
     *       to insert the app region into a specific part of the html page
     *       use the insertAppRegion() method.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance that
     *                              represents the app region to add.
     *
     * @return bool True if app region was added, false otherwise.
     *
     * @see IHtmlPage::insertAppRegion()
     */
    public function addAppRegion(IAppRegion $appRegion): bool
    {
        return !empty(array_push($this->appRegions, $appRegion));
    }

    /**
     * Remove an app region from the html page.
     *
     * @param string $appRegionName The name of the app region to remove.
     *
     * @return bool True if app region was removed, false otherwise.
     */
    public function removeAppRegion(string $appRegionName): bool
    {
        /**
         * @var IAppRegion $appRegion
         */
        foreach ($this->appRegions as $key => $appRegion) {
            if ($appRegion->getName() === $appRegionName) {
                unset($this->appRegions[$key]);
                return true;
            }
        }
        error_log(sprintf('IHtmlPage implementation Error: Failed to remove app region "%s".', $appRegionName));
        return false;
    }

    /**
     * Insert an app region into the html page near the specified neighbor.
     *
     * Note: If there are not any app regions already assigned to the html page
     *       this method will utilize the addAppRegion() method to add the
     *       specified app region to the html page.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance that
     *                              represents the app region to insert.
     *
     * @param string $neighbor The name of the neighboring app region used to
     *                         locate where in the html page the specified app
     *                         region will be inserted.
     *
     *                         Note: If the specified neighbor does not exist,
     *                               the app region will not be added to the
     *                               html page, an error will be logged, and
     *                               this method will return false, with one
     *                               exception as described in the next note.
     *
     *                        Note: If there are not any app regions already
     *                              assigned to the html page, then the
     *                              specified app region will be added to
     *                              the page via the addAppRegion() method
     *                              despite the fact that in this context
     *                              the specified $neighbor would not exist.
     *                              In this context this method will return
     *                              the boolean value returned by the call
     *                              to the addAppRegion() method.
     *
     *
     * @param int $insertMode (Optional) Determines whether the specified app region
     *                                   will be inserted above or below the specified
     *                                   neighbor. It is recommended that either the
     *                                   IHtmlPage::PREPEND or the IHtmlPage::APPEND
     *                                   interface constant be used to set this
     *                                   parameter's value.
     *                                   (Defaults to IHtmlPage::APPEND)
     *
     * @return bool True if app region was inserted successfully, false otherwise.
     *
     * @see IHtmlPage::addAppRegion()
     * @see IHtmlPage::PREPEND
     * @see IHtmlPage::APPEND
     */
    public function insertAppRegion(IAppRegion $appRegion, string $neighbor, int $insertMode = 2): bool
    {
        /**
         * If there are not any app regions already assigned to the page, simply add
         * the app region via the addAppRegion() method.
         */
        if (empty($this->getAppRegions()) === true) {
            return $this->addAppRegion($appRegion);
        }
        $splitArrays = ArrayUtility::splitArrayAtValue($this->getAppRegions(), $this->getAppRegion($neighbor), ($insertMode === IHtmlPage::PREPEND ? true : false));
        if (count($splitArrays) === 2) {
            unset($this->appRegions);
            $this->appRegions = array_merge($splitArrays[0], [$appRegion], $splitArrays[1]);
            return true;
        }
        error_log(sprintf('Failed to insert app region "%s" into the "%s" html page. The specified neighbor "%s" does not exist.', $appRegion->getName(), $this->getName(), $neighbor));
        return false;
    }

    /**
     * Returns an array of the IAppRegion implementation instances assigned
     * to the html page.
     *
     * @return array|IAppRegion An array of the IAppRegion implementation instances
     *                          assigned to the html page.
     */
    public function getAppRegions(): array
    {
        return $this->appRegions;
    }

    /**
     * Returns the specified IAppRegion implementation instance, or
     * an empty default IAppRegion implementation instance if the
     * specified IAppRegion implementation instance is not assigned
     * to the html page.
     *
     * @param string $regionName The name of the app region to get.
     *
     * @return IAppRegion The specified IAppRegion implementation instance, or an empty
     *                    default IAppRegion implementation instance if the specified
     *                    IAppRegion implementation instance is not assigned to the
     *                    html page.
     */
    public function getAppRegion(string $regionName): IAppRegion
    {
        /**
         * @var IAppRegion $appRegion
         */
        foreach ($this->appRegions as $appRegion) {
            if ($appRegion->getName() === $regionName) {
                return $appRegion;
            }
        }
        return new AppRegion('', IAppRegion::UN_CONTAINED, '', []);
    }

    /**
     * Returns an array of the names of all the apps assigned to the html page
     * ordered according to their respective app regions.
     *
     * @return array An array of the names of all the apps assigned to this
     *               html page ordered according to their respective app regions.
     */
    public function getAllAppNames(): array
    {
        $appNames = array();
        /**
         * @var IAppRegion $appRegion
         */
        foreach ($this->appRegions as $appRegion) {
            foreach ($appRegion->getAppNames() as $appName) {
                array_push($appNames, $appName);
            }
        }
        return $appNames;
    }
}
