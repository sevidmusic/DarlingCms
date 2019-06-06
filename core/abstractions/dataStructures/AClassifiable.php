<?php

namespace DarlingCms\abstractions\dataStructures;

use DarlingCms\interfaces\dataStructures\IClassifiable;

/**
 * Class AClassifiable. Defines an abstract implementation of the IClassifiable interface
 * that can be used as a base by classes that implement the IClassifiable interface.
 *
 * @package DarlingCms\abstractions\dataStructures
 */
class AClassifiable implements IClassifiable
{
    /**
     * @var string The name of the object.
     */
    protected $name = '';

    /**
     * @var string The instances's type.
     */
    protected $type = '';

    /**
     * @var string The instance's description.
     */
    protected $description = '';

    /**
     * Returns this instance's name.
     *
     * @return string This instance's name.
     */
    public function getName(): string
    {
        return $this->getName();
    }

    /**
     * Returns this instance's type.
     *
     * @return string This instance's type.
     */
    public function getType(): string
    {
        return $this->getType();
    }

    /**
     * Returns this instance's description.
     *
     * @return string This instance's description.
     */
    public function getDescription(): string
    {
        return $this->getDescription();
    }


}
