<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/4/17
 * Time: 5:01 PM
 */

namespace DarlingCms\interfaces\initializer;


interface Iinitializer
{
    /**
     * @return bool True if initialization was successful, false otherwise.
     */
    public function initialize();

    /**
     * @return array Get an array of the initialized.
     */
    public function getInitialized();

}
