<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2/5/17
 * Time: 4:38 PM
 */

namespace DarlingCms\classes\startup;

/**
 * Class themeStartup. Responsible for constructing the appropriate html link tags for each enabled theme.
 * @package DarlingCms\classes\startup
 */
class themeStartup extends \DarlingCms\abstractions\startup\Astartup
{

    /**
     * @var array Array of enabled themes.
     */
    private $enabledThemes;

    /**
     * @var array Array of <link> tags for each enabled theme.
     */
    private $themeLinkTags;

    /**
     * themeStartup constructor. Initializes the enabledThemes and themeLinkTags arrays.
     */
    public function __construct()
    {
        /* Determine which themes are enabled. */
        $this->enabledThemes = $this->determineEnabledThemes();
        /* Initialize themeLinkTags array. */
        $this->themeLinkTags = array();
    }

    /**
     * Determines which themes are enabled.
     *
     * @return array Array of enabled themes. An empty array will be returned if there are no enabled themes.
     */
    final private function determineEnabledThemes()
    {
        return array('darlingCms', 'helloWorld', 'helloUniverse', 'crudTester');
    }

    /**
     * Returns the enabledThemes array.
     *
     * @return array The enabled themes array.
     */
    public function getEnabledThemes()
    {
        return $this->enabledThemes;
    }

    /**
     * Display the theme link tags.
     *
     * This method has no return value.
     */
    public function displayThemeLinks()
    {
        foreach ($this->getThemeLinkTags() as $themeLinkTags) {
            foreach ($themeLinkTags as $themeLinkTag) {
                echo PHP_EOL . $themeLinkTag . PHP_EOL;
            }
        }
    }

    /**
     * Returns the themeLinkTags array.
     *
     * @return array Array of theme <link> tags that were constructed on startup().
     */
    protected function getThemeLinkTags()
    {
        return $this->themeLinkTags;
    }

    /**
     * Resets the themeLinkTags array.
     *
     * @return bool True if themeLinkTags array was reset, false otherwise.
     */
    protected function stop()
    {
        unset($this->themeLinkTags);
        $this->themeLinkTags = array();
        return empty($this->themeLinkTags);
    }

    /**
     * Constructs a theme <link> tag for each enabled theme and adds it to the themeLinkTags array.
     *
     * @return bool True if there were no errors, false otherwise.
     */
    protected function run()
    {
        /* Loop through enabled themes. */
        foreach ($this->enabledThemes as $enabledTheme) {
            /* Check that theme file exists. */
            if (file_exists(str_replace('core/classes/startup', 'themes/', __DIR__) . "$enabledTheme/$enabledTheme.css") === true) {
                /* Create an appropriately formatted <link> tag for this theme's stylesheet. */
                $this->themeLinkTags[$enabledTheme][] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://localhost:8888/DarlingCms/themes/$enabledTheme/$enabledTheme.css\">";
                /* Theme file exists, move onto the next $enabledTheme. */
                continue;
            }
            /* Theme file does not exist, register error. */
            $this->registerError('Startup Error for theme "' . $enabledTheme . '"',
                "
                      An error occurred while attempting to startup the \"$enabledTheme\" theme.
                      Please check the following:
                        - Is the \"$enabledTheme\" theme installed?
                        - Does the \"$enabledTheme\" theme's directory name match \"$enabledTheme\"?
                        - Does the \"$enabledTheme\" theme's css file name match \"$enabledTheme.css\"?
                    " . PHP_EOL
            );
        }
        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();
        /* Return true if there were no errors, false otherwise. */
        return empty($this->getErrors());
    }
}
