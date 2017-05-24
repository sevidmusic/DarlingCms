<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 5/4/17
 * Time: 5:22 PM
 */

namespace DarlingCms\abstractions\initializer;


abstract class Ainitializer implements \DarlingCms\interfaces\initializer\Iinitializer
{
    /**
     * @var array Array of initialized items.
     */
    protected $initialized = array();

    /**
     * @return bool True if initialization was successful, false otherwise.
     */
    abstract public function initialize();

    /**
     * @return array Array of things that were initialized.
     */
    public function getInitialized()
    {
        return $this->initialized;
    }

}
