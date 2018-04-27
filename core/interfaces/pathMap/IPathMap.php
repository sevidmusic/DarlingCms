<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 10:47 AM
 */

namespace DarlingCms\interfaces\pathMap;

/**
 * Interface IPathMap. Defines the contract of an object that defines an array of paths.
 * @package DarlingCms\interfaces\pathMap
 * @see IPathMap::getPaths()
 */
interface IPathMap
{
    /**
     * Returns an array of paths, or an empty array if there are no paths.
     * @return array An array of paths, or an empty array if there are no paths.
     */
    public function getPaths(): array;
}
