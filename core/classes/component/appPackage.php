<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/26/17
 * Time: 9:11 PM
 */

namespace DarlingCms\classes\component;

/**
 * Class appPackage
 * @package DarlingCms\classes\component Defines an implementaion of the DarlingCms\classes\component\app class
 * that organizes multiple app components into a package so they can be handled as one. This class is useful for
 * organizing apps that are tightly knit together either through dependency or purpose. An example would be a group
 * of apps related to core, instead of handling them as separate apps, a "Core" app package could be defined for any
 * apps related to core, making it possible to manage all "Core" apps together.
 */
class appPackage extends app
{
    public function apps()
    {
        return $this->getComponentAttributeValue('customAttributes')['apps'];
    }

}
