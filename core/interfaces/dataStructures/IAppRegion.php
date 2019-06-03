<?php


namespace DarlingCms\interfaces\dataStructures;

/**
 * Interface IAppRegion. Defines the basic contract of an object that represents
 * a data structure that can be used to infer the order of apps in a region.
 *
 * Note: In the context of the Darling Cms, a region is typically used to
 * group apps together in a some way.
 *
 * Note: It is important to keep in mind that IAppRegion implementations MUST
 * not generate output, they are simply data structures that can be used to
 * organize apps by name, and also to infer the order of said apps.
 *
 * @package DarlingCms\interfaces\dataStructures
 */
interface IAppRegion extends IClassifiable
{

    /**
     * @var int Interface constant that defines the value that can be passed to
     *          the insertApp() method's $insertMode parameter to indicate that
     *          the app should be inserted before the specified neighbor.
     */
    const PREPEND = 0;

    /**
     * @var int Interface constant that defines the value that can be passed to
     *          the insertApp() method's $insertMode parameter to indicate that
     *          the app should be inserted after the specified neighbor.
     */
    const APPEND = 2;

    /**
     * @var int Interface constant that defines a value that can be
     *          used to indicate that this IAppRegion implementation
     *          instance type is contained. For example, this value may
     *          be returned by the implementations getType() method.
     *
     * Note: This value is specifically designed to be used in regards to
     *       the app regions type.
     *
     * @see IAppRegion::getType()
     */
    const CONTAINED = 'contained';

    /**
     * @var int Interface constant that defines a value that can be
     *          used to indicate that this IAppRegion implementation
     *          instance type is un-contained. For example, this value may
     *          be returned by the implementations getType() method.
     *
     * Note: This value is specifically designed to be used in regards to
     *       the app regions type.
     *
     * @see IAppRegion::getType()
     */
    const UN_CONTAINED = 'un-contained';

    /**
     * Add a app to the end of the region.
     *
     * @param string $appName The name of the app to add.
     *
     * @return bool True if app was added, false otherwise.
     */
    public function addApp(string $appName): bool;

    /**
     * Remove an app from the region.
     *
     * @param string $appName The name of the app to remove.
     *
     * @return bool True if app was removed, false otherwise.
     */
    public function removeApp(string $appName): bool;

    /**
     * Insert an app into the region near the specified neighbor.
     *
     * @param string $appName The name of the app to insert.
     *
     * @param string $neighbor The name of the neighboring app used to locate where in the
     *                         region the specified app will be inserted.
     *
     * @param int $insertMode Determines whether the specified app will be inserted
     *                        above or below the specified neighbor. It is recommended
     *                        the IAppRegion::PREPEND and IAppRegion::APPEND constants
     *                        be used to set this parameter's value.
     *                        (Defaults to IAppRegion::APPEND)
     *
     * @return bool True is app was inserted successfully, false otherwise.
     *
     * @see IAppRegion::PREPEND
     * @see IAppRegion::APPEND
     */
    public function insertApp(string $appName, string $neighbor, int $insertMode = 2): bool;

    /**
     * Returns an ordered array of the names of the apps that belong to the region.
     *
     * @return array An ordered array of the names of the apps that belong to the region.
     */
    public function getAppNames(): array;

}
