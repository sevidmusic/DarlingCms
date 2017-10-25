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
 *
 * @todo: This class should be phased out of core. See the dev note below for more information.
 *
 * DEV NOTE:
 * Though the idea of an app "package" is seemingly useful for organization, in practice, the benefit of making it
 * part of core is not clear, and the overhead of developing this object so it is in fact beneficial seems mute
 * since app components already do a great job of organizing themselves based on dependency.
 *
 * It seems more appropriate that an app be developed to provide a GUI for enabling, and disabling apps, and said app
 * could take on the responsibility of organizing apps into logical "packages" in the GUI. The idea of a "package"
 * is more beneficial to the user, then it is to core, it's more of a GUI thing. The one benefit of a "package"
 * is apps could be enabled/disabled as one, though that is also something that seems more appropriate for a
 * app's GUI then as a pillar of the core logic since core already organizes apps based on the idea of
 * dependency, and does not really care about their relation to each other in terms of functionality,
 * nor should it.
 *
 * Apps are really intended to function like objects, with niche responsibilities. Grouping them into "packages"
 * seems to work in opposition of the idea that each app should have it's own unique responsibility independent
 * of other apps.
 *
 * Essentially, this breaks down the independence of apps, and could potentially encourage app
 * developers to become lazy about developing apps that function independently of one another, which goes against
 * the principals of development with the Darling Cms.
 *
 * For example, if one app malfunctions it should not adversely effect other apps, even if their functionality is
 * closely tied. The point is to discourage "real" dependency as much as possible. The only reason apps are allowed to
 * declare dependencies is so their can be some logical startup order. For example, an app that generates the opening
 * html tags for the page should most likely startup before an app the generates the closing html tags for the page,
 * If the former app malfunctions it should not inhibit the latter app's ability to generate the closing html
 * tags for the page.
 *
 * This is a long note, but this is all important to consider since the Darling Cms aims to be as S.O.L.I.D in design
 * as it can be, including in the the development of apps and themes. Again, the idea of a package really seems more
 * like a GUI thing, An app may provide a GUI that groups all apps related to admin tasks together in the GUI,
 * but core doesn't care about what apps are related to admin tasks, it does'nt know what an admin task is. The only
 * concern Core has is that it can successfully startup the apps that are enabled in a logical startup order.
 *
 * In other words, app dependency in the Darling Cms is more of a, "hey, i want to start up after that
 * other app" then a "i need that other app to function properly".
 */
class appPackage extends app
{
    public function apps()
    {
        return $this->getComponentAttributeValue('customAttributes')['apps'];
    }

}
