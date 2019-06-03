<?php


namespace DarlingCms\interfaces\dataStructures;

/**
 * Interface IClassifiable. Defines the basic contract of an object that
 * is classifiable, i.e., an object that has a name, a type, and a
 * description.
 *
 * @package DarlingCms\interfaces\dataStructures
 */
interface IClassifiable
{

    /**
     * Returns this instance's name.
     * @return string This instance's name.
     */
    public function getName(): string;

    /**
     * Returns this instance's type.
     * @return string This instance's type.
     */
    public function getType(): string;

    /**
     * Returns this instance's description.
     * @return string This instance's description.
     */
    public function getDescription(): string;
}
