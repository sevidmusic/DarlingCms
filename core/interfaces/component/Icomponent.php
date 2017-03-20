<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/19/17
 * Time: 10:23 PM
 */

namespace DarlingCms\interfaces\component;


interface Icomponent
{
    public function getComponentName();

    public function getComponentId();

    public function getComponentAttributes();

    public function getComponentType();

}