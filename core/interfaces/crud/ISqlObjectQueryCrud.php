<?php
/**
 * Created by Sevi Darling.
 */

namespace DarlingCms\interfaces\crud;

/**
 * Interface ISqlObjectQueryCrud. Defines an implementation of the ISqlQueryCrud
 * interface that defines the basic contract of an object that provides methods
 * that perform logic that is common to performing CRUD operations on object
 * instance data in a Sql database.
 *
 * @package DarlingCms\interfaces\crud
 */
interface ISqlObjectQueryCrud extends ISqlQueryCrud
{
    /**
     * Properly formats the given classname as a fully qualified namespace.
     *
     * @param string $className The classname.
     *
     * @return string The formatted class name. This will include the fully qualified
     *                namespace.
     *
     *                For example:
     *
     *                formatClassName('Some\Namespace\SomeClass');
     *
     *                The call above would return '\\Some\\Namespace\\SomeClass'
     *
     */
    public function formatClassName(string $className): string;
}
