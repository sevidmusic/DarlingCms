<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/13/17
 * Time: 10:30 AM
 */

namespace DarlingCms\classes\component;

/**
 * Class htmlContainer. Responsible for generating "containing" tags for one or more html components.
 * For example, the following php:
 * <?php
 * $c = new htmlContainer('div', array('class="container"'),
 *                      new html('p', 'hello'),
 *                      new html('a', 'world', 'href="#world"')
 * );
 * echo $c->getHtml();
 * ?>
 *
 * would generate the following html:
 * <div class="container">
 * <p>Hello</p>
 * <a href="#world">world</a>
 * </div>
 * @package DarlingCms\classes\component
 */
class htmlContainer extends \DarlingCms\classes\component\html\html
{
    /**
     * htmlContainer constructor.
     * @param string $tagType The tag type to use for the container.
     * @param array $attributes Array of attributes for the container.
     * @param html[] ...$html The html component(s) to be contained.
     */
    public function __construct(string $tagType, array $attributes, \DarlingCms\classes\component\html\html ...$html)
    {
        if (is_array($attributes) === false) {
            $attributes = array();
        }

        foreach ($html as $htmlComponent) {
            $this->appendHtml($htmlComponent);
        }

        parent::__construct($tagType, $this->content, $attributes);
    }

    /**
     * Appends an html component's html to the containers content.
     *
     * @param html[] ...$htmlComponent The html component(s) to append to the container's content.
     * @return bool True if successful, false otherwise.
     */
    public function appendHtml(\DarlingCms\classes\component\html\html ...$htmlComponent)
    {
        foreach ($htmlComponent as $component) {
            $this->content .= PHP_EOL . $component->getHtml() . PHP_EOL;
        }
        return $this->generateHtml();
    }

    /**
     * Prepends an html component's html to the containers content.
     *
     * @param html[] ...$htmlComponent The html component(s) to prepend to the containers content.
     * @return bool True if successful, false otherwise.
     */
    public function prependHtml(\DarlingCms\classes\component\html\html ...$htmlComponent)
    {
        array_reverse($htmlComponent);
        foreach ($htmlComponent as $component) {
            $this->content = PHP_EOL . $component->getHtml() . PHP_EOL . $this->content . PHP_EOL;
        }
        return $this->generateHtml();
    }
}
