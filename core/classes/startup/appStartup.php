<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2/2/17
 * Time: 11:16 PM
 */

namespace DarlingCms\classes\startup;


class appStartup extends \DarlingCms\abstractions\startup\Astartup
{
    /**
     * @var array Array of enabled apps.
     */
    private $enabledApps;

    public function __construct()
    {
        $this->enabledApps = array('helloWorld', 'helloUniverse');
    }

    /**
     * @inheritDoc
     */
    protected function stop()
    {
        foreach ($this->enabledApps as $enabledApp) {
            echo '<p>Shutting down ' . __DIR__ . '/' . $enabledApp . '</p>';
        }
        $this->registerError('Runtime error', 'An error occurred while attempting to shutdown the enabled apps.' . '<br>');
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function run()
    {
        foreach ($this->enabledApps as $enabledApp) {
            echo '<p>Starting up ' . __DIR__ . '/' . $enabledApp . '</p>';
        }
        $this->registerError('Runtime error', 'An error occurred while attempting to startup the enabled apps.');
        return true;
    }

}