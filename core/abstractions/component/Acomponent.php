<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 3/20/17
 * Time: 3:54 PM
 */

namespace DarlingCms\abstractions\component;


abstract class Acomponent implements \DarlingCms\interfaces\component\Icomponent
{
    protected $componentName;
    protected $componentAttributes;
    private $componentId;
    private $componentType;

    /**
     * Acomponent constructor. Sets the componentId, and the componentType.
     */
    public function __construct()
    {
        $this->setComponentId();
        $this->setComponentType();
    }

    final private function setComponentId()
    {
        $this->componentId = random_int(PHP_INT_MIN, PHP_INT_MAX);
        return (isset($this->componentId));
    }

    final private function setComponentType()
    {
        $this->componentType = get_class($this);
        return (isset($this->componentType));
    }

    public function getComponentName()
    {
        return $this->componentName;
    }

    abstract public function setComponentName(string $name);

    public function getComponentId()
    {
        return $this->componentId;
    }

    public function getComponentAttributes()
    {
        return $this->componentAttributes;
    }

    abstract public function setComponentAttributes(array $attributes);

    public function getComponentType()
    {
        return $this->componentType;
    }

}