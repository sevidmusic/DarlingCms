<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/29/18
 * Time: 6:49 PM
 */

namespace DarlingCms\interfaces\installer;


interface Iinstaller
{
    /**
     * Perform installation.
     * @return bool Return true on success, false on failure.
     */
    public function install(): bool;

    /**
     * Perform un-installation.
     * @return bool Return true on success, false on failure.
     */
    public function unInstall(): bool;
}
