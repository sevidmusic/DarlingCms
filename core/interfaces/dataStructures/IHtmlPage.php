<?php


namespace DarlingCms\interfaces\dataStructures;

/**
 * Interface IHtmlPage. Defines the basic contract of an object that represents
 * an organized collection of IAppRegion implementation instances that make up
 * a specific html page.
 *
 * @package DarlingCms\interfaces\dataStructures
 */
interface IHtmlPage extends IClassifiable
{
    /**
     * @var int Interface constant that defines the value that can be passed to the
     *          insertAppRegion() method's $insertMode parameter to indicate that
     *          the app region should be inserted before the specified neighbor.
     */
    const PREPEND = 0;

    /**
     * @var int Interface constant that defines the value that can be passed to the
     *          insertAppRegion() method's $insertMode parameter to indicate that
     *          the app region should be inserted after the specified neighbor.
     *          (This SHOULD be the default).
     */
    const APPEND = 2;

    /**
     * Add an app region to this IHtmlPage implementation instance.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance that
     *                              represents the app region to add.
     *
     * @return bool True if app region was added, false otherwise.
     */
    public function addAppRegion(IAppRegion $appRegion): bool;

    /**
     * Remove an app region from this IHtmlPage implementation instance.
     *
     * @param string $appRegionName The name of the app region to remove.
     *
     * @return bool True if app region was removed, false otherwise.
     */
    public function removeAppRegion(string $appRegionName): bool;

    /**
     * Insert an app region into this IHtmlPage implementation instance near
     * the specified neighbor.
     *
     * @param IAppRegion $appRegion The IAppRegion implementation instance that
     *                              represents the app region to insert.
     *
     * @param string $neighbor The name of the neighboring app region used to
     *                         locate where in the IHtmlPage implementation
     *                         instance the specified app region will be
     *                         inserted.
     *
     * @param int $insertMode Determines whether the specified app region will be inserted
     *                        above or below the specified neighbor. It is recommended
     *                        the IHtmlPage::PREPEND and IHtmlPage::APPEND constants
     *                        be used to set this parameter's value.
     *                        (Defaults to IHtmlPage::APPEND)
     *
     * @return bool True if app region was inserted successfully, false otherwise.
     *
     * @see IHtmlPage::PREPEND
     * @see IHtmlPage::APPEND
     */
    public function insertAppRegion(IAppRegion $appRegion, string $neighbor, int $insertMode = 2): bool;

    /**
     * Returns an array of the IAppRegion implementation instances assigned
     * to this IHtmlPage implementation instance.
     *
     * @return array|IAppRegion An array of the IAppRegion implementation instances
     *                          assigned to this IHtmlPage implementation instance.
     */
    public function getAppRegions(): array;


    /**
     * Returns the specified IAppRegion implementation instance, or
     * a default IAppRegion implementation instance if the specified
     * IAppRegion implementation instance is not assigned to this
     * IHtmlPage implementation instance.
     *
     * @param string $regionName The name of the app region to get.
     *
     * @return IAppRegion The specified IAppRegion implementation instance, or
     *                    a default IAppRegion implementation instance if the specified
     *                    IAppRegion implementation instance is not assigned to this
     *                    IHtmlPage implementation instance.
     */
    public function getAppRegion(string $regionName): IAppRegion;

    /**
     * Returns an array of the names of all the apps assigned to this IHtmlPage
     * implementation instance ordered according to their respective app regions.
     *
     * @return array An array of the names of all the apps assigned to this
     *               IHtmlPage implementation instance ordered according to
     *               their respective app regions.
     */
    public function getAllAppNames(): array;
}
