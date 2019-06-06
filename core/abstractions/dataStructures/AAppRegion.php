<?php


namespace DarlingCms\abstractions\dataStructures;


use DarlingCms\classes\staticClasses\utility\ArrayUtility;
use DarlingCms\interfaces\dataStructures\IAppRegion;

/**
 * Class AAppRegion. Defines an abstract implementation of the IAppRegion interface
 * that can be used as a base for classes that implement the IAppRegion interface.
 * @package DarlingCms\abstractions\dataStructures
 */
abstract class AAppRegion implements IAppRegion
{
    /**
     * @var array Array of the names of the apps that belong to the region.
     */
    protected $appNames = array();

    /**
     * @var string The name of the region.
     */
    protected $name = '';

    /**
     * @var string The region's type.
     */
    protected $type = '';

    /**
     * @var string The region's description.
     */
    protected $description = '';

    /**
     * AAppRegion constructor. Sets the region's name, type, description, and
     * assigns the specified apps to the region.
     *
     * @param string $name The name to assign the region.
     *
     * @param string $type The region's type.
     *
     *                     Note: Only the following interface constants
     *                           should be used to set the region's type:
     *
     *                           IAppRegion::CONTAINED
     *
     *                           IAppRegion::UN_CONTAINED
     *
     * @param string $description A description of the region.
     *
     * @param array $appNames An array of the names of the apps to initially assign
     *                        to the region.
     *
     * @see IAppRegion::CONTAINED
     * @see IAppRegion::UN_CONTAINED
     */
    public function __construct(string $name, string $type, string $description, array $appNames = array())
    {
        $this->name = $name;
        $this->setType($type);
        $this->description = $description;
        $this->appNames = $appNames;
    }

    /**
     * Sets the regions type. This method will validate that the specified
     * type matches the value of one of the following interface constants:
     *
     * IAppRegion::CONTAINED
     *
     * IAppRegion::UN_CONTAINED
     *
     * Note: If the specified $type does not match one of the above interface constants,
     *       this method will log an error and return false.
     *
     * @param string $type The type to assign to the region.
     *
     *                           Note: Only the following interface constants
     *                           should be used to set the region's type:
     *
     *                           IAppRegion::CONTAINED
     *
     *                           IAppRegion::UN_CONTAINED
     *
     * @return bool True if the region's type was set, false otherwise.
     *
     * @see IAppRegion::CONTAINED
     * @see IAppRegion::UN_CONTAINED
     */
    protected function setType(string $type): bool
    {
        $validTypes = array(IAppRegion::CONTAINED, IAppRegion::UN_CONTAINED);
        if (in_array($type, $validTypes, true) === true) {
            $this->type = $type;
            return true;
        }
        error_log(sprintf('IAppRegion Implementation Error: Invalid app region type "%s". Type must be one of the following: "%s", or "%s". Defaulting to "%s".', $type, IAppRegion::CONTAINED, IAppRegion::UN_CONTAINED, IAppRegion::CONTAINED));
        $this->type = IAppRegion::CONTAINED;
        return false;
    }

    /**
     * Add a app to the end of the region.
     *
     * @param string $appName The name of the app to add.
     *
     * @return bool True if app was added, false otherwise.
     */
    public function addApp(string $appName): bool
    {
        return !empty(array_push($this->appNames, $appName));
    }

    /**
     * Remove an app from the region.
     *
     * @param string $appName The name of the app to remove.
     *
     * @return bool True if app was removed, false otherwise.
     */
    public function removeApp(string $appName): bool
    {
        foreach ($this->appNames as $key => $name) {
            if ($name === $appName) {
                unset($this->appNames[$key]);
                return true;
            }
        }
        error_log(sprintf("IAppRegion Implementation Error: Failed to remove app %s", $appName));
        return false;
    }

    /**
     * Insert an app into the region near the specified neighbor.
     *
     * @param string $appName The name of the app to insert.
     *
     * @param string $neighbor The name of the neighboring app used to locate where in the
     *                         region the specified app will be inserted.
     *
     * @param int $insertMode Determines whether the specified app will be inserted
     *                        above or below the specified neighbor. It is recommended
     *                        the IAppRegion::PREPEND and IAppRegion::APPEND constants
     *                        be used to set this parameter's value.
     *                        (Defaults to IAppRegion::APPEND)
     *
     * @return bool True is app was inserted successfully, false otherwise.
     *
     * @see IAppRegion::PREPEND
     * @see IAppRegion::APPEND
     */
    public function insertApp(string $appName, string $neighbor, int $insertMode = 2): bool
    {
        $splitArrays = $this->splitArrayAtValue($this->getAppNames(), $neighbor, ($insertMode === IAppRegion::PREPEND ? true : false));
        unset($this->appNames);
        $this->appNames = array_merge($splitArrays[0], [$appName], $splitArrays[1]);
        return true;
    }

    /**
     * Returns an ordered array of the names of the apps that belong to the region.
     *
     * @return array An ordered array of the names of the apps that belong to the region.
     */
    public function getAppNames(): array
    {
        return $this->appNames;
    }

    /**
     * Returns the region's name.
     *
     * @return string The region's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the region's description.
     *
     * @return string The region's description.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns the region's description.
     *
     * @return string The region's description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns the key of a specified value in the specified array.
     *
     * Note: If the value exists at more than key, the key for the first occurrence
     *       is returned.
     *
     * Note: If the specified value does not exist in the specified array this method
     *       will return false.
     *
     * Note: This method utilizes the ArrayUtility::getValuesKey() method internally.
     *
     * Examples:
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz', 'Foo')); // returns 2
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz', 'FooKey' => 'Foo')); // returns FooKey
     *
     *   getValuesByKey('Foo', array('Bar', 'Baz')); // returns false, Foo does not exist!
     *
     * @param mixed $value The value whose key should be returned
     *
     * @param array $array The array to search.
     *
     * @return bool|int|string The value's key, or false if the value's key could not be
     *                         determined.
     *
     * @see ArrayUtility::getValuesKey()
     */
    protected function getValuesKey($value, array $array)
    {
        return ArrayUtility::getValuesKey($value, $array);
    }

    /**
     * Splits an array into two arrays at the specified value.
     *
     * Note: Which of the two arrays the $value ends up in is determine by the $splitBefore
     *       parameter. If set to true, the $value will be placed at the end of the first
     *       of the two resulting arrays, if set to false the $value will placed at the
     *       beginning of the second of the two resulting arrays.
     *
     *       For example:
     *
     *           splitArrayAtValue(array(1,2,3,4), 3, $splitBefore = true)
     *           // returns array(array(1,2,3), array(4))
     *
     *           splitArrayAtValue(array(1,2,3,4), 3, $splitBefore = false)
     *           // returns array(array(1,2), array(3,4))
     *
     * Note: This method utilizes the ArrayUtility::splitArrayAtValue() method internally.
     *
     * @param array $array The array to split.
     *
     * @param mixed $value The value to split the array at.
     *
     * @param bool $splitBefore Determines which of the two resulting arrays the $value
     *                          will end up in. If set to true, the $value will be the
     *                          last item in the first of the two resulting arrays, if
     *                          set to false the $value will be the first item in the
     *                          second of the two resulting arrays.
     *
     * @return array An array containing the arrays resulting from the split. The
     *               first array at index 0 will contain the values that came before
     *               the specified $value, and the second array at index 1 will contain
     *               the values that came after the specified $value.
     *
     *               <br>Example: array(0 => [VALUES BEFORE VALUE], 0 => [VALUES AFTER VALUE])
     *
     *               <br>Also Note: The $splitBefore parameter determines which of the arrays
     *                              the value will be assigned to.
     *
     *               <br>Note: Original indexes will not be preserved in the resulting arrays.
     *
     * @see ArrayUtility::splitArrayAtValue()
     */
    protected function splitArrayAtValue(array $array, $value, $splitBefore = false): array
    {
        return ArrayUtility::splitArrayAtValue($array, $value, $splitBefore);
    }
}
