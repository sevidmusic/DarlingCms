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


class themeStartup extends \DarlingCms\abstractions\startup\Astartup
{

    private $enabledThemes;

    private $themeLinkTags;

    /**
     * themeStartup constructor.
     * @param $enabledThemes
     */
    public function __construct()
    {
        $this->enabledThemes = array('darlingCms','helloWorld', 'helloUniverse');
        $this->themeLinkTags = array();
    }

    public function getEnabledThemes()
    {
        return $this->enabledThemes;
    }

    public function displayThemeLinks()
    {
        foreach ($this->getThemeLinkTags() as $themeLinkTags) {
            foreach ($themeLinkTags as $themeLinkTag) {
                echo PHP_EOL . $themeLinkTag . PHP_EOL;
            }
        }
    }

    protected function getThemeLinkTags()
    {
        return $this->themeLinkTags;
    }

    /**
     * @inheritDoc
     */
    protected function stop()
    {
        // TODO: Implement stop() method.
    }

    /**
     * @inheritDoc
     */
    protected function run()
    {
        foreach ($this->enabledThemes as $enabledTheme) {
            $this->themeLinkTags[$enabledTheme][] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://localhost:8888/DarlingCms/themes/$enabledTheme/$enabledTheme.css\">";
        }

        /* Display any errors. (Errors will ony be displayed if error reporting is turned on.) */
        $this->displayErrors();

        return true;
    }
}